<?php

/**
 * Импорт товаров из таблицы (Google-таблица или CSV-файл)
 *
 * Первая строка - заголовки. Товар ищется по артикулу (vendor_code): есть - обновляем, нет - создаём.
 * Колонки (можно по-английски или по-русски):
 *   vendor_code | Артикул      - обязательно, уникальный
 *   name        | Название     - обязательно
 *   price       | Цена         - обязательно, больше нуля
 *   price_old   | Старая цена  - цена до скидки
 *   description | Описание
 *   sort        | Порядок      - меньше - выше в каталоге
 *   is_active   | В продаже    - да/нет, 1/0 (нет колонки - товар в продаже)
 *   P: <название>              - характеристика товара, например «P: Состав», «P: Цвет»
 * Товары, которых нет в таблице, снимаются с продажи (Config('shop')['import']['hide_missing']), но не удаляются: на них ссылаются заказы.
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
    ];

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
            if ($title === '') {
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
            throw new RuntimeException('Неизвестные колонки: ' . implode(', ', $unknown) . '. Характеристики называйте с префиксом «' . self::PARAM_PREFIX . ' », например «P: Цвет»');
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

        $error = match (true) {
            ($item['vendor_code'] ?? '') === ''    => 'нет артикула',
            ($item['name'] ?? '') === ''           => 'нет названия',
            !self::isNumber($item['price'] ?? '')  => "цена «{$item['price']}» - не число",
            self::toNumber($item['price']) <= 0    => 'цена должна быть больше нуля',
            isset($item['price_old']) && $item['price_old'] !== '' && !self::isNumber($item['price_old']) => "старая цена «{$item['price_old']}» - не число",
            default                                => '',
        };
        if ($error !== '') {
            $this->report['errors'][] = "Строка {$line}: {$error}, строка пропущена";
            return null;
        }

        return [
            'vendor_code' => $item['vendor_code'],
            'name'        => $item['name'],
            'price'       => self::toNumber($item['price']),
            'price_old'   => isset($item['price_old']) && $item['price_old'] !== '' ? self::toNumber($item['price_old']) : null,
            'description' => $item['description'] ?? null,
            'sort'        => isset($item['sort']) && self::isNumber($item['sort']) ? (int)self::toNumber($item['sort']) : null,
            'is_active'   => isset($item['is_active']) ? self::toBool($item['is_active']) : true,
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
        $product->price       = ShopOrderModel::toCents($item['price']) / 100;
        $product->is_active   = $item['is_active'];
        if ($item['price_old'] !== null || $isNew) {
            $product->price_old = ShopOrderModel::toCents((float)$item['price_old']) / 100;
        }
        if ($item['description'] !== null) {
            $product->description = htmlspecialchars($item['description']);
        }
        if ($item['sort'] !== null) {
            $product->sort = $item['sort'];
        }
        if ($item['params'] || $isNew) {
            $product->setParams($item['params']); // Характеристики заменяются целиком - как в таблице
        }
        $product->flush();

        $this->report[$isNew ? 'created' : 'updated']++;
    }

    // Google и Excel отделяют тысячи пробелом, неразрывным (\u{00A0}) или узким неразрывным (\u{202F}) пробелом, а дробь - запятой
    private static function normalizeNumber(string $value): string
    {
        return str_replace([',', ' ', "\u{00A0}", "\u{202F}", '₽', 'руб.', 'руб'], ['.', '', '', '', '', '', ''], trim($value));
    }

    private static function isNumber(string $value): bool
    {
        return is_numeric(self::normalizeNumber($value));
    }

    private static function toNumber(string $value): float
    {
        return (float)self::normalizeNumber($value);
    }

    private static function toBool(string $value): bool
    {
        return !in_array(mb_strtolower(trim($value)), ['0', 'нет', 'no', 'false', '-', 'off'], true);
    }
}
