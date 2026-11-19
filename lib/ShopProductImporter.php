<?php

/**
 * Импорт товаров из таблицы (Google-таблица или CSV-файл)
 *
 * Первая строка - заголовки. Товар ищется по артикулу (vendor_code): есть - обновляем, нет - создаём.
 * Колонки (можно по-английски или по-русски):
 *   vendor_code | Артикул      - обязательно, уникальный
 *   name        | Название     - обязательно
 *   price       | Цена         - обязательно у товаров в продаже, больше нуля (у снятых с продажи можно пусто)
 *   price_old   | Старая цена  - цена до скидки
 *   description | Описание
 *   sort        | Порядок      - меньше - выше в каталоге
 *   is_active   | В продаже    - да/нет, 1/0 (нет колонки - товар в продаже)
 *   variant_group | Группа вариантов - один код у одного товара в разных цветах/размерах
 *   related     | Покупают вместе - артикулы через запятую
 *   stock       | Остаток      - число (в единицах товара, можно дробное); пусто - не учитывать
 *   category    | Категория    - путь через «/»: «Одежда / Футболки»; недостающие категории создаются
 *   unit        | Единица      - шт, м, м², кг, г, л, уп (Config('shop')['units']); цена - за неё
 *   qty_step    | Шаг          - шаг количества в корзине: 0.1 (м), 0.5 (кг); пусто - min_step единицы (по умолчанию 1)
 *   qty_min     | Минимум      - минимальный заказ в единицах товара; пусто - один шаг
 *   similar     | Похожие      - «Похожие товары»: артикулы через запятую
 *   addon_groups | Доп. услуги - названия групп доп. услуг (ShopAddonGroupModel) через «;» или запятую; пусто - без доп. услуг
 *   images      | Картинки     - ссылки или имена файлов через запятую (первая - главная), см. syncImages().
 *                              Имена файлов ищутся в SHOP_IMPORT_IMAGES_SOURCE: публичная папка Яндекс Диска, начало ссылки или локальная папка
 *   P: <название>              - характеристика товара, например «P: Состав», «P: Цвет»
 *   # <что угодно>             - служебная колонка, не импортируется (заметки, старые данные)
 * Товары, которых нет в таблице, снимаются с продажи (Config('shop')['import']['hide_missing']), но не удаляются: на них ссылаются заказы.
 *
 * Картинки скачиваются не во время импорта, а потом порциями (syncImages): фото бывают по 10 МБ, а товаров - сотни.
 * Уже скачанные повторно не качаются: у товара помнится, из какого источника какая картинка (image_sources).
 * Если у товара в таблице есть картинки, то на сайте у него ровно они и в том же порядке (загруженные в админке заменятся);
 * пустая ячейка - картинки товара не трогаем, их можно вести в админке
 *
 *  $report = (new ShopProductImporter())->import(ShopProductImporter::parseCsv(ShopProductImporter::downloadGoogleSheet($url)));
 */
class ShopProductImporter
{
    const PARAM_PREFIX = 'P:';

    private const FIELDS = [
        'vendor_code' => ['артикул'],
        'name'        => ['название', 'наименование'],
        'price'       => ['цена'],
        'price_old'   => ['старая цена', 'цена до скидки'],
        'description' => ['описание', 'desc'],
        'sort'        => ['порядок', 'сортировка'],
        'is_active'   => ['в продаже', 'активен'],
        'variant_group' => ['группа вариантов', 'группа'],
        'related'     => ['покупают вместе', 'сопутствующие'],
        'similar'     => ['похожие', 'похожие товары', 'аналоги'],
        'addon_groups' => ['доп. услуги', 'доп услуги', 'доп. товары', 'дополнительно', 'группы доп. услуг'],
        'images'      => ['картинки', 'фото', 'изображения'],
        'stock'       => ['остаток', 'количество', 'склад'],
        'category'    => ['категория', 'раздел'],
        'unit'        => ['единица', 'ед', 'ед.', 'единица измерения'],
        'qty_step'    => ['шаг', 'шаг количества'],
        'qty_min'     => ['минимум', 'мин. количество', 'минимальное количество'],
    ];

    // Заголовки при выгрузке (и первые русские названия для импорта)
    private const EXPORT_TITLES = [
        'vendor_code'   => 'Артикул',
        'name'          => 'Название',
        'category'      => 'Категория',
        'price'         => 'Цена',
        'unit'          => 'Единица',
        'qty_step'      => 'Шаг',
        'qty_min'       => 'Минимум',
        'price_old'     => 'Старая цена',
        'is_active'     => 'В продаже',
        'stock'         => 'Остаток',
        'variant_group' => 'Группа вариантов',
        'related'       => 'Покупают вместе',
        'similar'       => 'Похожие',
        'addon_groups'  => 'Доп. услуги',
        'sort'          => 'Порядок',
        'images'        => 'Картинки',
        'description'   => 'Описание',
    ];

    const SKIP_PREFIX = '#'; // Колонки «# ...» не импортируются

    /**
     * Весь каталог в CSV в формате импорта: выгрузить -> вставить в Google-таблицу -> вести там -> импортировать обратно.
     * С BOM, чтобы Excel правильно открыл кириллицу
     */
    public static function exportCsv(): string
    {
        $products = (new ShopProductModel())->getAll();
        $decode   = fn($value) => html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'); // В БД строки экранированы
        $number   = fn(float $value) => rtrim(rtrim(number_format($value, 2, '.', ''), '0'), '.');

        $paramNames = [];
        foreach ($products as $product) {
            $paramNames += array_flip(array_keys($product->getParams()));
        }
        $paramNames = array_keys($paramNames);

        $out = fopen('php://temp', 'r+');
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, array_merge(array_values(self::EXPORT_TITLES), array_map(fn($name) => self::PARAM_PREFIX . ' ' . $decode($name), $paramNames)), ',', '"', '\\');
        usort($products, fn($a, $b) => [$a->sort, $a->id] <=> [$b->sort, $b->id]);
        foreach ($products as $product) {
            $params = $product->getParams();
            fputcsv($out, array_merge([
                $decode($product->vendor_code),
                $decode($product->name),
                $product->category_id ? $decode(ShopCategoryModel::pathName($product->category_id)) : '',
                ShopRubles($product->price), // В таблице - рубли, у нас - копейки
                $product->unitKey(),
                $product->qty_step > 0 ? $number($product->qty_step) : '',
                $product->qty_min > 0 ? $number($product->qty_min) : '',
                $product->price_old > 0 ? ShopRubles($product->price_old) : '',
                $product->is_active ? 'да' : 'нет',
                $product->track_stock ? $number($product->stock) : '',
                $decode($product->variant_group),
                $decode($product->related),
                $decode($product->similar),
                implode('; ', array_filter(array_map(fn($id) => ($g = new ShopAddonGroupModel($id))->isExists() ? $decode($g->name) : null, $product->addonGroupIds()))),
                (string)$product->sort,
                implode(', ', self::exportImages($product)),
                $decode($product->description),
            ], array_map(fn($name) => $decode($params[$name] ?? ''), $paramNames)), ',', '"', '\\');
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return $csv;
    }

    // Картинки для выгрузки: откуда они были импортированы, а загруженные в админке - полными ссылками на этот сайт
    private static function exportImages(ShopProductModel $product): array
    {
        $sources = array_column(self::imageSources($product), 1, 0); // [источник => имя]
        $byName  = array_flip(array_filter($sources, 'strlen'));
        return array_map(fn($image) => $byName[$image['name']] ?? ShopAbsUrl($image['url']), $product->getImages());
    }

    // ---- Источники данных ----

    // Ссылка на CSV-выгрузку из обычной ссылки на Google-таблицу (или просто её ID). Лист берётся из gid в ссылке
    public static function googleSheetCsvUrl(string $urlOrId): string
    {
        $urlOrId = trim($urlOrId);
        if (preg_match('~/spreadsheets/d/([\w-]+)~', $urlOrId, $m)) {
            $docId = $m[1];
        } elseif (preg_match('~^[\w-]{20,}$~', $urlOrId)) {
            $docId = $urlOrId;
        } else {
            throw new InvalidArgumentException('Не похоже на ссылку на Google-таблицу');
        }
        $gid = preg_match('~[#&?]gid=(\d+)~', $urlOrId, $m) ? $m[1] : '0';
        return "https://docs.google.com/spreadsheets/d/{$docId}/export?format=csv&gid={$gid}";
    }

    public static function downloadGoogleSheet(string $urlOrId): string
    {
        $ch = curl_init(self::googleSheetCsvUrl($urlOrId));
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 60,
        ]);
        $csv         = curl_exec($ch);
        $code        = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $contentType = (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $error       = curl_error($ch);
        curl_close($ch);

        if ($csv === false) {
            throw new RuntimeException("Не удалось скачать таблицу: {$error}");
        }
        // Закрытая таблица отдаёт не CSV, а страницу входа в Google
        if ($code !== 200 || stripos($contentType, 'text/html') !== false) {
            throw new RuntimeException("Google не отдал таблицу (HTTP {$code}). Откройте доступ «Все, у кого есть ссылка» → «Читатель»");
        }
        return $csv;
    }

    public static function parseCsv(string $csv): array
    {
        $csv    = preg_replace('/^\xEF\xBB\xBF/', '', $csv); // BOM из Excel
        $first  = strtok($csv, "\n");
        $sep    = substr_count((string)$first, ';') > substr_count((string)$first, ',') ? ';' : ','; // Русский Excel сохраняет через ";"
        $rows   = [];
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $csv);
        rewind($stream);
        while (($row = fgetcsv($stream, 0, $sep, '"', '\\')) !== false) {
            $rows[] = array_map(fn($v) => trim((string)$v), $row);
        }
        fclose($stream);
        return $rows;
    }

    // ---- Импорт ----

    private array $report;

    /**
     * Возвращает отчёт: ['created' => n, 'updated' => n, 'hidden' => n, 'errors' => ['Строка 5: ...', ...]].
     * Если заголовки неправильные или нет ни одной правильной строки - бросает исключение и ничего не меняет
     */
    public function import(array $rows, ?bool $hideMissing = null): array
    {
        $hideMissing  = $hideMissing ?? (bool)Config('shop')['import']['hide_missing'];
        $this->report = ['created' => 0, 'updated' => 0, 'hidden' => 0, 'errors' => []];

        $columns = $this->parseHeader($rows[0] ?? []);
        $items   = [];
        foreach (array_slice($rows, 1, null, true) as $i => $row) {
            if (implode('', $row) === '') {
                continue; // Пустая строка
            }
            $item = $this->parseRow($columns, $row, $i + 1);
            if ($item === null) {
                continue;
            }
            if (isset($items[$item['vendor_code']])) {
                $this->report['errors'][] = "Строка " . ($i + 1) . ": артикул «{$item['vendor_code']}» уже был выше, строка пропущена";
                continue;
            }
            $items[$item['vendor_code']] = $item;
        }
        if (empty($items)) {
            throw new RuntimeException('В таблице нет ни одного правильного товара, каталог не изменён' . ($this->report['errors'] ? ': ' . implode('; ', array_slice($this->report['errors'], 0, 5)) : ''));
        }

        new ShopProductModel(); // CREATE TABLE IF NOT EXISTS - до транзакции, иначе DDL её неявно завершит
        new ShopCategoryModel();
        new ShopAddonGroupModel();
        DbTransaction(function ($db) use ($items, $hideMissing) {
            foreach ($items as $item) {
                $this->save($item);
            }
            if ($hideMissing) {
                $this->report['hidden'] = (int)$db->query(
                    "UPDATE `shop_products` SET `is_active` = 0, `last_update_time` = ?d WHERE `is_active` = 1 AND `vendor_code` NOT IN (?a)",
                    time(),
                    array_map('htmlspecialchars', array_keys($items))
                );
            }
        });
        ShopStockSubscriptionModel::notifyAvailable(); // Товары снова в наличии - пишем тем, кто ждал
        return $this->report;
    }

    // [номер колонки => 'name' | 'P:Состав']
    private function parseHeader(array $header): array
    {
        $aliases = [];
        foreach (self::FIELDS as $field => $names) {
            foreach (array_merge([$field], $names) as $name) {
                $aliases[mb_strtolower($name)] = $field;
            }
        }

        $columns = [];
        $unknown = [];
        foreach ($header as $k => $title) {
            $title = trim($title);
            if ($title === '' || str_starts_with($title, self::SKIP_PREFIX)) {
                continue;
            }
            if (stripos($title, self::PARAM_PREFIX) === 0) {
                $param = trim(mb_substr($title, mb_strlen(self::PARAM_PREFIX)));
                if ($param !== '') {
                    $columns[$k] = self::PARAM_PREFIX . $param;
                }
                continue;
            }
            $field = $aliases[mb_strtolower($title)] ?? null;
            if ($field === null) {
                $unknown[] = '«' . htmlspecialchars($title) . '»';
            } else {
                $columns[$k] = $field;
            }
        }

        if ($unknown) {
            throw new RuntimeException('Неизвестные колонки: ' . implode(', ', $unknown) . '. Характеристики называйте с префиксом «' . self::PARAM_PREFIX . ' », например «P: Цвет», а колонки, которые не нужно импортировать, - с «' . self::SKIP_PREFIX . '»');
        }
        foreach (['vendor_code' => 'Артикул', 'name' => 'Название', 'price' => 'Цена'] as $field => $title) {
            if (!in_array($field, $columns, true)) {
                throw new RuntimeException("В первой строке нет колонки «{$title}» ({$field})");
            }
        }
        return $columns;
    }

    private function parseRow(array $columns, array $row, int $line): ?array
    {
        $item = ['params' => []];
        foreach ($columns as $k => $field) {
            $value = $row[$k] ?? '';
            if (str_starts_with($field, self::PARAM_PREFIX)) {
                $item['params'][htmlspecialchars(mb_substr($field, mb_strlen(self::PARAM_PREFIX)))] = htmlspecialchars($value);
            } else {
                $item[$field] = $value;
            }
        }

        // Цена нужна только товарам в продаже: снятые с продажи можно держать в таблице и без цены
        $isActive = isset($item['is_active']) ? self::toBool($item['is_active']) : true;
        $noPrice  = !$isActive && trim($item['price'] ?? '') === '';
        $error    = match (true) {
            ($item['vendor_code'] ?? '') === ''    => 'нет артикула',
            ($item['name'] ?? '') === ''           => 'нет названия',
            $noPrice                               => '',
            ShopKopecks($item['price'] ?? '') === null => "цена «{$item['price']}» - не число",
            $isActive && ShopKopecks($item['price']) <= 0 => 'цена должна быть больше нуля',
            isset($item['price_old']) && $item['price_old'] !== '' && ShopKopecks($item['price_old']) === null => "старая цена «{$item['price_old']}» - не число",
            isset($item['stock']) && $item['stock'] !== '' && !self::isNumber($item['stock']) => "остаток «{$item['stock']}» - не число",
            isset($item['unit']) && $item['unit'] !== '' && self::unitKey($item['unit']) === null => "единица «{$item['unit']}» неизвестна, можно: " . implode(', ', array_keys(Config('shop')['units'])),
            isset($item['qty_step']) && $item['qty_step'] !== '' && (!self::isNumber($item['qty_step']) || self::toNumber($item['qty_step']) <= 0) => "шаг «{$item['qty_step']}» - не положительное число",
            isset($item['qty_min']) && $item['qty_min'] !== '' && !self::isNumber($item['qty_min']) => "минимум «{$item['qty_min']}» - не число",
            default                                => '',
        };
        if ($error !== '') {
            $this->report['errors'][] = "Строка {$line}: {$error}, строка пропущена";
            return null;
        }

        return [
            'vendor_code' => $item['vendor_code'],
            'name'        => $item['name'],
            'price'       => $noPrice ? 0 : (int)ShopKopecks($item['price']), // Рубли из таблицы -> копейки (без float)
            'price_old'   => isset($item['price_old']) && $item['price_old'] !== '' ? (int)ShopKopecks($item['price_old']) : null,
            'description' => $item['description'] ?? null,
            'sort'        => isset($item['sort']) && self::isNumber($item['sort']) ? (int)self::toNumber($item['sort']) : null,
            'is_active'   => isset($item['is_active']) ? self::toBool($item['is_active']) : true,
            'variant_group' => $item['variant_group'] ?? null,
            'related'     => $item['related'] ?? null,
            'similar'     => $item['similar'] ?? null,
            'addon_groups' => isset($item['addon_groups']) ? array_values(array_filter(array_map('trim', preg_split('/[;,\n]+/', $item['addon_groups'])), 'strlen')) : null,
            // Пустая ячейка «Картинки» - колонка есть, но ничего не указано: картинки не трогаем (иначе импорт стирал фото)
            'images'      => isset($item['images']) && trim((string)$item['images']) !== ''
                ? array_values(array_unique(array_filter(array_map('trim', preg_split('/[,;\n]+/', $item['images'])), 'strlen')))
                : null,
            'stock'       => array_key_exists('stock', $item) ? ($item['stock'] === '' ? '' : max(0, round(self::toNumber($item['stock']), 3))) : null, // null - колонки нет, '' - не учитывать
            'category'    => $item['category'] ?? null, // null - колонки нет, '' - без категории
            'unit'        => isset($item['unit']) && $item['unit'] !== '' ? self::unitKey($item['unit']) : (isset($item['unit']) ? Config('shop')['default_unit'] : null),
            'qty_step'    => isset($item['qty_step']) && $item['qty_step'] !== '' ? round(self::toNumber($item['qty_step']), 3) : (isset($item['qty_step']) ? 0.0 : null), // 0 - min_step единицы
            'qty_min'     => isset($item['qty_min']) && $item['qty_min'] !== '' ? round(self::toNumber($item['qty_min']), 3) : (isset($item['qty_min']) ? 0.0 : null),
            'params'      => $item['params'],
        ];
    }

    private function save(array $item): void
    {
        // Строки в БД храним с экранированным HTML - так же, как их сохраняют формы (Input::clean)
        $vendorCode = htmlspecialchars($item['vendor_code']);
        $product    = ShopProductModel::findByVendorCode($vendorCode);
        $isNew      = !$product->isExists();

        $product->vendor_code = $vendorCode;
        $product->name        = htmlspecialchars($item['name']);
        $product->price       = $item['price'];
        $product->is_active   = $item['is_active'];
        if ($item['price_old'] !== null || $isNew) {
            $product->price_old = (int)$item['price_old'];
        }
        if ($item['description'] !== null) {
            $product->description = htmlspecialchars($item['description']);
        }
        if ($item['sort'] !== null) {
            $product->sort = $item['sort'];
        }
        if ($item['variant_group'] !== null) {
            $product->variant_group = htmlspecialchars($item['variant_group']);
        }
        if ($item['related'] !== null) {
            $product->related = htmlspecialchars(implode(', ', array_filter(array_map('trim', explode(',', $item['related'])), 'strlen')));
        }
        if ($item['addon_groups'] !== null) {
            $ids = [];
            foreach ($item['addon_groups'] as $groupName) {
                $group = ShopAddonGroupModel::findByName($groupName);
                if ($group->isExists()) {
                    $ids[] = (int)$group->id;
                } else {
                    $this->report['errors'][] = "{$item['vendor_code']}: нет группы доп. услуг «{$groupName}» (товар загружен без неё)";
                }
            }
            $product->setAddonGroupIds($ids);
        }
        if ($item['similar'] !== null) {
            $product->similar = htmlspecialchars(implode(', ', array_filter(array_map('trim', explode(',', $item['similar'])), 'strlen')));
        }
        if ($item['stock'] !== null) {
            $product->track_stock = $item['stock'] !== '';
            $product->stock       = $item['stock'] === '' ? 0 : $item['stock'];
        }
        if ($item['category'] !== null) {
            $product->category_id = $item['category'] === '' ? 0 : ShopCategoryModel::findOrCreateByPath(htmlspecialchars($item['category']));
        }
        if ($item['unit'] !== null || $isNew) {
            $product->unit = $item['unit'] ?? Config('shop')['default_unit'];
        }
        if ($item['qty_step'] !== null || $isNew) {
            $product->qty_step = $item['qty_step'] ?? 0.0;
        }
        if ($item['qty_min'] !== null || $isNew) {
            $product->qty_min = $item['qty_min'] ?? 0.0;
        }
        if ($item['params'] || $isNew) {
            $product->setParams($item['params']); // Характеристики заменяются целиком - как в таблице
        }
        $product->flush();
        if ($item['images'] !== null) {
            self::setImageSources($product, $item['images']);
            $product->flush();
        }

        $this->report[$isNew ? 'created' : 'updated']++;
    }

    // ---- Картинки ----

    const IMAGES_LOG  = 'tmp/shop_images.log';  // Журнал скачивания: каждая картинка и её результат (видно в админке на странице импорта)
    const IMAGES_LOCK = 'tmp/shop_images.lock'; // Чтобы админка и крон не качали одно и то же одновременно

    // [[источник, имя картинки ('' - ещё не скачана, '-причина' - не скачалась)], ...]
    private static function imageSources(ShopProductModel $product): array
    {
        $sources = json_decode((string)$product->image_sources, true);
        return is_array($sources) ? array_values(array_filter($sources, fn($s) => is_array($s) && count($s) === 2)) : [];
    }

    private static function isFailed(string $name): bool
    {
        return str_starts_with($name, '-');
    }

    // Имена скачанных картинок (без нескачанных и неудавшихся)
    private static function downloadedNames(array $sources): array
    {
        return array_values(array_filter(array_map('strval', array_column($sources, 1)), fn($n) => $n !== '' && !self::isFailed($n)));
    }

    /**
     * Запоминает, какие картинки должны быть у товара. Уже скачанные из того же источника сохраняются, неудавшиеся - пробуем ещё раз.
     * Ссылки на картинки этого же товара на этом сайте (так их выгружает exportCsv) скачивать не нужно
     */
    private static function setImageSources(ShopProductModel $product, array $wanted): void
    {
        $known  = array_column(self::imageSources($product), 1, 0);
        $own    = array_column($product->getImages(), 'name');
        $prefix = ShopAbsUrl(ShopImage::dir((int)$product->id));
        $ret    = [];
        foreach ($wanted as $source) {
            $name = (string)($known[$source] ?? '');
            if ($name === '' && str_starts_with($source, $prefix) && preg_match('~/([a-f0-9]{12})\.jpg$~', $source, $m) && in_array($m[1], $own, true)) {
                $name = $m[1];
            }
            $ret[] = [$source, self::isFailed($name) ? '' : $name];
        }
        // Картинки, которые убрали из таблицы, удаляем сразу
        $removed = array_diff(self::downloadedNames(array_map(fn($n) => [null, $n], array_values($known))), array_column($ret, 1));
        foreach ($removed as $name) {
            ShopImage::delete((int)$product->id, $name);
        }
        if ($removed) {
            $product->images = json_encode(array_values(array_diff($own, $removed)));
        }
        $product->image_sources = json_encode($ret, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // Сколько товаров ждут скачивания картинок или пересборки их списка
    public static function countImagesPending(): int
    {
        return count(self::pendingImageProductIds());
    }

    // Товары с картинками из таблицы: [id => ['vendor_code', 'images' => [...], 'sources' => [...]]]
    private static function importedImageRows(): array
    {
        new ShopProductModel();
        $ret = [];
        foreach (GetDefaultDb()->select("SELECT `id`, `vendor_code`, `images`, `image_sources` FROM `shop_products` WHERE `image_sources` IS NOT NULL AND `image_sources` NOT IN ('', '[]') ORDER BY `sort`, `id`") as $row) {
            $ret[(int)$row['id']] = [
                'vendor_code' => (string)$row['vendor_code'],
                'images'      => json_decode((string)$row['images'], true) ?: [],
                'sources'     => json_decode((string)$row['image_sources'], true) ?: [],
            ];
        }
        return $ret;
    }

    private static function pendingImageProductIds(): array
    {
        $ret = [];
        foreach (self::importedImageRows() as $id => $row) {
            $hasNew = in_array('', array_map('strval', array_column($row['sources'], 1)), true);
            if ($hasNew || self::downloadedNames($row['sources']) !== $row['images']) {
                $ret[] = $id;
            }
        }
        return $ret;
    }

    /**
     * Состояние картинок из таблицы (для админки): сколько товаров ведут картинки в таблице, у скольких они есть,
     * сколько файлов скачано, сколько товаров ждут и какие файлы не скачались (с причиной)
     */
    public static function imagesStatus(): array
    {
        $rows   = self::importedImageRows();
        $status = ['products' => count($rows), 'with_images' => 0, 'files' => 0, 'files_total' => 0, 'pending' => count(self::pendingImageProductIds()), 'failed' => [], 'is_running' => self::isSyncRunning()];
        foreach ($rows as $id => $row) {
            $status['with_images'] += $row['images'] ? 1 : 0;
            $status['files_total'] += count($row['sources']);
            $status['files']       += count(self::downloadedNames($row['sources']));
            foreach ($row['sources'] as [$source, $name]) {
                if (self::isFailed((string)$name)) {
                    $status['failed'][] = ['product_id' => $id, 'vendor_code' => $row['vendor_code'], 'source' => $source, 'error' => ltrim((string)$name, '-') ?: 'не скачалась'];
                }
            }
        }
        return $status;
    }

    // ---- Журнал скачивания ----

    public static function log(string $message): void
    {
        $file = BASEPATH . self::IMAGES_LOG;
        // Журнал не растёт бесконечно: больше 2 МБ - оставляем последний 1 МБ
        if (is_file($file) && filesize($file) > 2 << 20) {
            $tail = (string)file_get_contents($file, false, null, filesize($file) - (1 << 20));
            file_put_contents($file, substr($tail, strpos($tail, "\n") + 1), LOCK_EX);
        }
        file_put_contents($file, date('d.m H:i:s') . ' ' . str_replace("\n", ' ', $message) . "\n", FILE_APPEND | LOCK_EX);
    }

    /**
     * Новые строки журнала после позиции $offset (байт). $offset < 0 - последние $limit строк.
     * Возвращает ['offset' => новая позиция, 'lines' => [...]]. Если журнал обрезали - отдаём с начала
     */
    public static function readLog(int $offset, int $limit = 200): array
    {
        $file = BASEPATH . self::IMAGES_LOG;
        clearstatcache(true, $file);
        $size = is_file($file) ? (int)filesize($file) : 0;
        if ($size === 0) {
            return ['offset' => 0, 'lines' => []];
        }
        if ($offset < 0 || $offset > $size) {
            $from  = max(0, $size - ($limit * 300));
            $text  = (string)file_get_contents($file, false, null, $from);
            $lines = explode("\n", rtrim($text, "\n"));
            if ($from > 0) {
                array_shift($lines); // Первая строка могла обрезаться посередине
            }
            return ['offset' => $size, 'lines' => array_slice($lines, -$limit)];
        }
        $text = (string)file_get_contents($file, false, null, $offset, min($size - $offset, 1 << 20));
        $end  = strrpos($text, "\n");
        if ($end === false) {
            return ['offset' => $offset, 'lines' => []]; // Строка ещё дописывается
        }
        return ['offset' => $offset + $end + 1, 'lines' => explode("\n", substr($text, 0, $end))];
    }

    // Идёт ли сейчас скачивание (держит ли кто-то блокировку)
    public static function isSyncRunning(): bool
    {
        $lock = fopen(BASEPATH . self::IMAGES_LOCK, 'c');
        if (!$lock) {
            return false;
        }
        $isFree = flock($lock, LOCK_EX | LOCK_NB);
        $isFree && flock($lock, LOCK_UN);
        fclose($lock);
        return !$isFree;
    }

    /**
     * Скачивает картинки из колонки «Картинки», начиная новые не позже чем через $seconds секунд (одна картинка - до ~70 секунд:
     * скачивание до 60 и уменьшение, поэтому $seconds должно быть меньше лимита запроса минимум на минуту). Порядок картинок у товара - как в таблице,
     * картинки, которых в таблице больше нет, удаляются. Вызывать повторно, пока left > 0 (админка делает это сама, крон - в shop_import).
     * Каждая картинка пишется в журнал (IMAGES_LOG). Одновременно качает только один процесс: если занято - busy = true.
     * $who - кто запустил, для журнала: «админка», «крон».
     * Возвращает ['downloaded' => n, 'left' => сколько товаров ещё ждут, 'errors' => [...], 'busy' => bool]
     */
    public static function syncImages(int $seconds = 40, string $who = 'админка'): array
    {
        $report = ['downloaded' => 0, 'left' => 0, 'errors' => [], 'busy' => false];
        $lock   = fopen(BASEPATH . self::IMAGES_LOCK, 'c');
        if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) {
            $report['busy'] = true;
            $report['left'] = self::countImagesPending();
            return $report;
        }
        try {
            $start = time();
            $ids   = self::pendingImageProductIds();
            if (!$ids) {
                return $report;
            }
            self::log("── Начало ({$who}): в очереди товаров - " . count($ids) . ', источник - ' . (self::sourceTitle() ?: 'ссылки из таблицы'));
            foreach ($ids as $i => $id) {
                if (time() - $start >= $seconds) {
                    $report['left'] = count($ids) - $i;
                    break;
                }
                $product = new ShopProductModel($id);
                $sources = self::imageSources($product);
                $isAll   = true;
                $code    = html_entity_decode((string)$product->vendor_code, ENT_QUOTES); // В БД строки экранированы, журнал - простой текст
                foreach ($sources as $k => [$source, $name]) {
                    if ($name !== '') {
                        continue;
                    }
                    if (time() - $start >= $seconds) {
                        $isAll = false;
                        break;
                    }
                    $t = microtime(true);
                    self::log("{$code}: {$source} - скачиваю…");
                    try {
                        $sources[$k][1] = self::downloadImage($source, (int)$product->id);
                        $report['downloaded']++;
                        self::log("{$code}: {$source} - готово, " . number_format(microtime(true) - $t, 1, ',', '') . ' с');
                    } catch (Throwable $e) {
                        $error          = mb_substr(trim($e->getMessage()), 0, 200);
                        $sources[$k][1] = '-' . $error; // Не скачалась (с причиной) - пробуем снова при следующем импорте
                        $report['errors'][] = "{$code}: {$source} - {$error}";
                        self::log("{$code}: {$source} - НЕ СКАЧАЛАСЬ: {$error}");
                    }
                    // Сохраняем после каждой картинки: если запрос оборвётся по таймауту, скачанное не потеряется
                    $product->image_sources = json_encode($sources, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $product->flush();
                }
                $product->image_sources = json_encode($sources, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                if ($isAll) {
                    // Картинки товара - ровно как в таблице и в её порядке: остальные (старые, загруженные вручную) удаляем
                    $names = self::downloadedNames($sources);
                    foreach (array_diff(array_column($product->getImages(), 'name'), $names) as $old) {
                        ShopImage::delete((int)$product->id, $old);
                    }
                    $product->images = json_encode($names);
                } else {
                    $report['left'] = count($ids) - $i;
                }
                $product->flush();
                if (!$isAll) {
                    break;
                }
            }
            self::log("── Конец ({$who}): скачано - {$report['downloaded']}, не скачалось - " . count($report['errors']) . ", товаров в очереди - {$report['left']}");
            return $report;
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /**
     * Ссылка на файл картинки: полная ссылка - как есть; имя файла - из Config('shop')['import']['images_source']
     * (публичная папка Яндекс Диска или начало ссылки)
     */
    private static function sourceTitle(): string
    {
        return (string)Config('shop')['import']['images_source'];
    }

    public static function imageUrl(string $source): string
    {
        if (preg_match('~^https?://~i', $source)) {
            return $source;
        }
        $base = trim((string)Config('shop')['import']['images_source']);
        if ($base === '') {
            throw new RuntimeException('в таблице имя файла, а откуда брать файлы не задано: SHOP_IMPORT_IMAGES_SOURCE в .env');
        }
        if (preg_match('~^https?://(disk\.yandex\.[a-z]+|disk\.360\.yandex\.[a-z]+|yadi\.sk)/~i', $base)) {
            // Публичная папка Яндекс Диска: ссылку на скачивание файла даёт API
            $json = self::httpGet('https://cloud-api.yandex.net/v1/disk/public/resources/download?public_key=' . rawurlencode($base) . '&path=' . rawurlencode('/' . ltrim($source, '/')), 2 << 20);
            $href = json_decode($json, true)['href'] ?? '';
            if ($href === '') {
                throw new RuntimeException('нет такого файла на Яндекс Диске');
            }
            return $href;
        }
        return rtrim($base, '/') . '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($source, '/'))));
    }

    /**
     * Локальная папка с картинками (SHOP_IMPORT_IMAGES_SOURCE - не ссылка, а путь: абсолютный или от корня сайта, например tmp/import_images).
     * null - источник не папка
     */
    private static function localImagesDir(): ?string
    {
        $base = trim((string)Config('shop')['import']['images_source']);
        if ($base === '' || preg_match('~^https?://~i', $base)) {
            return null;
        }
        return rtrim(str_starts_with($base, '/') ? $base : BASEPATH . $base, '/') . '/';
    }

    private static function downloadImage(string $source, int $product_id): string
    {
        $dir = preg_match('~^https?://~i', $source) ? null : self::localImagesDir();
        if ($dir !== null) {
            $file = $dir . basename($source); // Только имя файла: из таблицы нельзя выйти за пределы папки
            if (!is_file($file)) {
                throw new RuntimeException('нет такого файла в папке ' . self::sourceTitle());
            }
            return ShopImage::saveFromFile($file, $product_id);
        }
        $tmp = tempnam(sys_get_temp_dir(), 'shopimg');
        try {
            self::httpGet(self::imageUrl($source), ShopImage::MAX_FILE, $tmp); // Сразу в файл: фото бывают по 10+ МБ
            return ShopImage::saveFromFile($tmp, $product_id);
        } finally {
            @unlink($tmp);
        }
    }

    // GET-запрос: ответ строкой или (если задан $toFile) в файл - без загрузки в память
    private static function httpGet(string $url, int $maxBytes, ?string $toFile = null): string
    {
        $file  = $toFile !== null ? fopen($toFile, 'wb') : null;
        $body  = '';
        $code  = 0;
        $error = '';

        // Редиректы проходим сами: так каждый следующий адрес тоже проверяется на «внутренний» (SSRF)
        for ($redirect = 0; $redirect <= 5; $redirect++) {
            self::assertPublicUrl($url);
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER   => $file === null,
                CURLOPT_FOLLOWLOCATION   => false,
                CURLOPT_CONNECTTIMEOUT   => 10,
                CURLOPT_TIMEOUT          => 60,
                CURLOPT_PROTOCOLS        => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_NOPROGRESS       => false,
                CURLOPT_PROGRESSFUNCTION => fn($ch, $total, $done) => $done > $maxBytes ? 1 : 0, // Не качаем больше лимита
            ]);
            if ($file) {
                curl_setopt($ch, CURLOPT_FILE, $file);
            }
            $body  = curl_exec($ch);
            $code  = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            $error = curl_error($ch);
            $next  = (string)curl_getinfo($ch, CURLINFO_REDIRECT_URL);
            curl_close($ch);

            if (!in_array($code, [301, 302, 303, 307, 308], true) || $next === '') {
                break;
            }
            if ($file) {
                ftruncate($file, 0); // От редиректа в файле не должно остаться ничего
                rewind($file);
            }
            $url = $next;
        }

        $file && fclose($file);
        if ($body === false || $code !== 200) {
            throw new RuntimeException($error !== '' ? $error : "HTTP {$code}");
        }
        return $file === null ? (string)$body : '';
    }

    /**
     * Адрес, по которому серверу разрешено ходить: только http(s) и только на публичный IP.
     * Иначе строка «картинки» в таблице заставляла сервер дёргать 127.0.0.1, 169.254.169.254 и внутренние сервисы
     */
    private static function assertPublicUrl(string $url): void
    {
        $parts  = parse_url($url);
        $scheme = strtolower((string)($parts['scheme'] ?? ''));
        $host   = strtolower(trim((string)($parts['host'] ?? ''), '[]'));
        if (!in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new RuntimeException('Ссылка должна быть http(s): ' . $url);
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if (!$ips) {
            throw new RuntimeException('Не удалось определить адрес ' . $host);
        }
        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw new RuntimeException('Запрещённый адрес: ' . $host . ' (' . $ip . ')');
            }
        }
    }

    // Google и Excel отделяют тысячи пробелом, неразрывным (\u{00A0}) или узким неразрывным (\u{202F}) пробелом, а дробь - запятой
    private static function normalizeNumber(string $value): string
    {
        return str_replace([',', ' ', "\u{00A0}", "\u{202F}", '₽', 'руб.', 'руб'], ['.', '', '', '', '', '', ''], trim($value));
    }

    // Только обычная запись: «1.5e2» или «0x1A» из таблицы - не число (is_numeric() их пропускает)
    private static function isNumber(string $value): bool
    {
        return (bool)preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)$/', self::normalizeNumber($value));
    }

    private static function toNumber(string $value): float
    {
        return (float)self::normalizeNumber($value);
    }

    // Единица из таблицы: «м», «М», «м.», «метр» -> ключ из Config('shop')['units'] (null - неизвестна)
    private static function unitKey(string $value): ?string
    {
        $value = mb_strtolower(rtrim(trim($value), '.'));
        $value = ['метр' => 'м', 'метры' => 'м', 'штука' => 'шт', 'штуки' => 'шт', 'кв.м' => 'м²', 'м2' => 'м²', 'кв. м' => 'м²', 'литр' => 'л', 'килограмм' => 'кг', 'грамм' => 'г', 'упаковка' => 'уп'][$value] ?? $value;
        foreach (Config('shop')['units'] as $key => $unit) {
            if ($value === mb_strtolower($key) || $value === mb_strtolower(rtrim($unit['name'], '.'))) {
                return $key;
            }
        }
        return null;
    }

    private static function toBool(string $value): bool
    {
        return !in_array(mb_strtolower(trim($value)), ['0', 'нет', 'no', 'false', '-', 'off'], true);
    }
}
