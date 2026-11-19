<?php

/**
 * Товар магазина. Характеристики (Состав, Размер, Цвет...) хранятся в JSON-поле params: getParams()/setParams()
 */
class ShopProductModel extends SiteModel
{
    use JsonFieldTrait;

    public function scheme()
    {
        return [
            'id'               => 'int',
            'name'             => 'string',
            'vendor_code'      => 'string',
            'variant_group'    => 'string', // Код группы вариантов: товары с одним кодом - это один товар в разных цветах/размерах
            'category_id'      => 'int', // ShopCategoryModel, 0 - без категории
            'price'            => 'int', // В копейках, за единицу (unit)
            'price_old'        => 'int', // Цена до скидки в копейках (0 - нет скидки)
            'unit'             => 'string', // Единица измерения: ключ из Config('shop')['units'] (шт, м, кг...)
            'qty_step'         => 'float', // Шаг количества в корзине: 1 шт., 0.1 м, 0.5 кг
            'qty_min'          => 'float', // Минимальное количество в заказе
            'description'      => 'string',
            'params'           => 'string',
            'images'           => 'string', // JSON: имена картинок (ShopImage), первая - главная
            'image_sources'    => 'string', // JSON: картинки из импорта [[источник, имя в images или ''], ...] (ShopProductImporter::syncImages)
            'related'          => 'string', // «Покупают вместе» вручную: артикулы через запятую
            'similar'          => 'string', // «Похожие товары» вручную: артикулы через запятую
            'addon_group_ids'  => 'string', // JSON: группы доп. услуг (ShopAddonGroupModel)
            'track_stock'      => 'bool', // Учитывать остаток
            'stock'            => 'float',
            'rating'           => 'float', // Средняя оценка по опубликованным отзывам (ShopReviewModel::recalcProduct)
            'reviews_count'    => 'int',
            'is_active'        => 'bool',
            'sort'             => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `vendor_code` VARCHAR(100) NOT NULL DEFAULT '',
                `variant_group` VARCHAR(100) NOT NULL DEFAULT '',
                `category_id` INT DEFAULT NULL,
                `price` BIGINT NOT NULL DEFAULT 0,
                `price_old` BIGINT NOT NULL DEFAULT 0,
                `unit` VARCHAR(20) NOT NULL DEFAULT 'шт',
                `qty_step` DECIMAL(10, 3) NOT NULL DEFAULT 1,
                `qty_min` DECIMAL(10, 3) NOT NULL DEFAULT 1,
                `description` TEXT DEFAULT NULL,
                `params` TEXT DEFAULT NULL,
                `images` TEXT DEFAULT NULL,
                `image_sources` TEXT DEFAULT NULL,
                `related` TEXT DEFAULT NULL,
                `similar` TEXT DEFAULT NULL,
                `addon_group_ids` TEXT DEFAULT NULL,
                `track_stock` TINYINT(1) NOT NULL DEFAULT 0,
                `stock` DECIMAL(12, 3) NOT NULL DEFAULT 0,
                `rating` DECIMAL(3, 2) NOT NULL DEFAULT 0,
                `reviews_count` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `is_active` (`is_active`, `sort`),
                KEY `vendor_code` (`vendor_code`),
                KEY `variant_group` (`variant_group`),
                KEY `category_id` (`category_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_products', $id);
    }

    public function jsonFieldName(): string
    {
        return 'params';
    }

    // Характеристики: ['Состав' => '100% хлопок', ...]
    public function getParams(): array
    {
        return array_filter($this->getJsonData(), fn($v) => $v !== '' && $v !== null);
    }

    public function setParams(array $params): void
    {
        $this->setJsonData(array_filter($params, fn($v) => $v !== '' && $v !== null));
    }

    // ---- Картинки ----

    private function imageNames(): array
    {
        $names = json_decode((string)$this->images, true);
        return is_array($names) ? array_values(array_filter($names, 'is_string')) : [];
    }

    private function setImageNames(array $names): void
    {
        $this->images = json_encode(array_values(array_unique($names)));
    }

    // [['name' => ..., 'url' => большая, 'thumb' => превью], ...]
    public function getImages(): array
    {
        return array_map(fn($name) => [
            'name'  => $name,
            'url'   => ShopImage::url($this->id, $name),
            'thumb' => ShopImage::url($this->id, $name, true),
        ], $this->imageNames());
    }

    public function mainImage(bool $thumb = true): string
    {
        $images = $this->getImages();
        return $images ? $images[0][$thumb ? 'thumb' : 'url'] : '';
    }

    public function addImageFromFile(string $path): string
    {
        $name = ShopImage::saveFromFile($path, $this->id);
        $this->setImageNames(array_merge($this->imageNames(), [$name]));
        return $name;
    }

    public function removeImage(string $name): void
    {
        ShopImage::delete($this->id, $name);
        $this->setImageNames(array_diff($this->imageNames(), [$name]));
        // Картинка была из импорта - забываем источник (при следующем импорте она скачается снова, если ещё есть в таблице)
        $sources = json_decode((string)$this->image_sources, true);
        if (is_array($sources)) {
            $this->image_sources = json_encode(array_values(array_filter($sources, fn($s) => ($s[1] ?? '') !== $name)), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
    }

    public function setMainImage(string $name): void
    {
        $names = $this->imageNames();
        if (in_array($name, $names, true)) {
            $this->setImageNames(array_merge([$name], array_diff($names, [$name])));
        }
    }

    public function delete()
    {
        $id  = $this->id;
        $ret = parent::delete();
        if ($ret) {
            ShopImage::deleteAll($id);
        }
        return $ret;
    }

    // ---- Адрес ----

    // /shop/p/22-royal-emerald: по id, название - для людей и поисковиков (неверное название - 301 на правильный адрес)
    public function slug(): string
    {
        return self::slugFromName((string)$this->name);
    }

    public static function slugFromName(string $name): string
    {
        return rtrim(substr(TextToUrl(html_entity_decode($name, ENT_QUOTES)), 0, 80), '-') ?: 'tovar';
    }

    public function url(): string
    {
        return SiteRoot("shop/p/{$this->id}-{$this->slug()}");
    }

    public static function urlById(int $id): string
    {
        return (new static($id))->url();
    }

    public function category(): ?ShopCategoryModel
    {
        $category = new ShopCategoryModel($this->category_id ?: null);
        return $category->isExists() ? $category : null;
    }

    // ---- Единицы измерения и количество ----

    public function unitKey(): string
    {
        $units = Config('shop')['units'];
        return isset($units[$this->unit]) ? $this->unit : Config('shop')['default_unit'];
    }

    // Подпись после количества: «шт.», «м», «кг»
    public function unitName(): string
    {
        return ShopUnitName($this->unitKey());
    }

    // Подпись после цены: «/м» (у штучных товаров - пусто)
    public function priceUnitSuffix(): string
    {
        $suffix = Config('shop')['units'][$this->unitKey()]['price'] ?? '';
        return $suffix === '' ? '' : "/{$suffix}";
    }

    // Минимальный шаг количества для единицы (Config('shop')['units'][...]['min_step']): шаг товара не бывает меньше
    public static function unitMinStep(string $unit): float
    {
        $minStep = (float)(Config('shop')['units'][$unit]['min_step'] ?? 1);
        return $minStep > 0 ? round($minStep, 3) : 1.0;
    }

    public function step(): float
    {
        return self::stepBy($this->unitKey(), (float)$this->qty_step);
    }

    public function minQuantity(): float
    {
        return self::minQuantityBy($this->unitKey(), (float)$this->qty_step, (float)$this->qty_min);
    }

    // То же без модели - для строк из БД (витрина считает наличие сразу для всех товаров)
    public static function stepBy(string $unit, float $qtyStep): float
    {
        $units   = Config('shop')['units'];
        $minStep = self::unitMinStep(isset($units[$unit]) ? $unit : Config('shop')['default_unit']);
        return $qtyStep > 0 ? max($minStep, round($qtyStep, 3)) : $minStep;
    }

    public static function minQuantityBy(string $unit, float $qtyStep, float $qtyMin): float
    {
        return max(self::stepBy($unit, $qtyStep), round($qtyMin, 3));
    }

    // Штучный товар - количество целое (так считаем «товаров в корзине»)
    public function isPieceUnit(): bool
    {
        return $this->unitKey() === 'шт' && fmod($this->step(), 1.0) == 0.0;
    }

    /**
     * Приводит количество к правилам товара: кратно шагу, не меньше минимума, не больше maxQuantity().
     * 0 и меньше - 0 (убрать из корзины). Если на складе меньше минимума - 0
     */
    public function normalizeQuantity($quantity, ?float $max = null): float
    {
        // $max - сколько ещё можно взять (например, если часть товара уже лежит в другой строке корзины с другими доп. услугами)
        $quantity = (float)str_replace(',', '.', (string)$quantity);
        if ($quantity <= 0) {
            return 0.0;
        }
        $step     = $this->step();
        $max      = min($this->maxQuantity(), $max ?? INF);
        $quantity = max($this->minQuantity(), round(round($quantity / $step) * $step, 3));
        if ($quantity > $max) {
            $quantity = round(floor(round($max / $step, 6)) * $step, 3); // Вниз до шага: остаток 2.75 м при шаге 0.5 -> 2.5
        }
        return $quantity >= $this->minQuantity() ? $quantity : 0.0;
    }

    // ---- Наличие ----

    public function isAvailable(): bool
    {
        return !$this->track_stock || $this->stock >= $this->minQuantity();
    }

    // Сколько можно положить в корзину (в единицах товара)
    public function maxQuantity(): float
    {
        $max = (float)Config('shop')['max_quantity'];
        return $this->track_stock ? max(0.0, min($max, round($this->stock, 3))) : $max;
    }

    // Подпись о наличии (только если остаток учитывается): ['text' => ..., 'css' => 'in|low|out']
    public function availability(): ?array
    {
        if (!$this->track_stock) {
            return null;
        }
        if (!$this->isAvailable()) {
            return ['text' => 'Нет в наличии', 'css' => 'out'];
        }
        return $this->stock <= (float)Config('shop')['low_stock']
            ? ['text' => 'Осталось ' . ShopQuantity($this->stock, $this->unitKey()), 'css' => 'low']
            : ['text' => 'В наличии', 'css' => 'in'];
    }

    // ---- Варианты (один товар в разных цветах/размерах) ----

    // Активные товары той же группы, включая этот. Пусто, если группы нет или в ней один товар
    public function getVariants(): array
    {
        if ($this->variant_group === '') {
            return [];
        }
        $ids = $this->db->selectCol(
            "SELECT `id` FROM ?# WHERE `variant_group` = ? AND (`is_active` = 1 OR `id` = ?d) ORDER BY `sort` ASC, `id` ASC",
            $this->table,
            $this->variant_group,
            $this->id
        );
        return count($ids) > 1 ? array_map(fn($id) => new static($id), $ids) : [];
    }

    /**
     * Переключатель вариантов: по каждой характеристике, которая у вариантов различается -
     * ['Цвет' => ['is_image' => true, 'options' => [['value' => 'Синий', 'product' => модель, 'is_current' => bool], ...]], ...].
     * При выборе значения ищем вариант, у которого остальные различающиеся характеристики как у текущего
     * (сменили цвет - размер остался). Если характеристики одинаковые - один ряд с названиями товаров
     */
    public function variantOptions(): array
    {
        $variants = $this->getVariants();
        if (!$variants) {
            return [];
        }
        // Сравниваем без учёта регистра и пробелов: «Хлопок» и «хлопок» - одно и то же значение
        $norm   = fn($value) => mb_strtolower(trim(html_entity_decode((string)$value)));
        $params = array_map(fn(self $v) => array_map($norm, $v->getParams()), $variants);
        $labels = array_map(fn(self $v) => $v->getParams(), $variants); // Как показывать значение

        $differing = [];
        foreach (array_unique(array_merge(...array_map('array_keys', $params))) as $name) {
            $values = array_unique(array_filter(array_map(fn($p) => $p[$name] ?? '', $params), 'strlen'));
            if (count($values) > 1) {
                $differing[] = $name;
            }
        }
        $current = array_map($norm, $this->getParams());

        if (!$differing) {
            return ['Вариант' => [
                'is_image' => true,
                'options'  => array_map(fn(self $v) => ['value' => $v->name, 'product' => $v, 'is_current' => $v->id === $this->id], $variants),
            ]];
        }

        $imageParams = array_map('mb_strtolower', (array)Config('shop')['variant_image_params']);
        $ret         = [];
        foreach ($differing as $name) {
            $options = [];
            foreach ($variants as $i => $variant) {
                $value = $params[$i][$name] ?? '';
                if ($value === '') {
                    continue;
                }
                // Насколько вариант совпадает с текущим по остальным различающимся характеристикам
                $score = 0;
                foreach ($differing as $other) {
                    if ($other !== $name && ($params[$i][$other] ?? '') === ($current[$other] ?? '')) {
                        $score++;
                    }
                }
                if (!isset($options[$value]) || $score > $options[$value]['score'] || $variant->id === $this->id) {
                    $options[$value] = ['value' => (string)$labels[$i][$name], 'product' => $variant, 'is_current' => ($current[$name] ?? '') === $value, 'score' => $variant->id === $this->id ? PHP_INT_MAX : $score];
                }
            }
            $ret[$name] = [
                'is_image' => in_array(mb_strtolower($name), $imageParams, true),
                'options'  => array_values($options),
            ];
        }
        return $ret;
    }

    // Сколько вариантов у каждой группы (для подписи «4 варианта» в каталоге)
    public static function variantCounts(): array
    {
        static $counts = null;
        if ($counts === null) {
            $counts = array_map('intval', GetDefaultDb()->selectCol(
                "SELECT `variant_group` AS ARRAY_KEY, COUNT(*) FROM `shop_products` WHERE `is_active` = 1 AND `variant_group` <> '' GROUP BY `variant_group`"
            ));
        }
        return $counts;
    }

    // ---- «Покупают вместе» ----

    /**
     * Сначала товары, указанные вручную (related), потом те, что чаще всего покупали вместе с этим в оплаченных заказах.
     * Варианты этого же товара не показываем
     */
    public function getBoughtTogether(int $limit = 4): array
    {
        return self::boughtTogether([$this->id], $limit);
    }

    // То же для нескольких товаров сразу (блок «Добавьте к заказу» в корзине)
    public static function boughtTogether(array $productIds, int $limit = 4): array
    {
        $productIds = array_values(array_filter(array_map('intval', $productIds)));
        if (!$productIds || $limit <= 0) {
            return [];
        }
        $db       = GetDefaultDb();
        $exclude  = array_flip($productIds);
        $groups   = array_filter($db->selectCol("SELECT DISTINCT `variant_group` FROM `shop_products` WHERE `id` IN (?a)", $productIds), 'strlen');
        $candidates = [];

        // Вручную: артикулы из поля related
        foreach ($db->selectCol("SELECT `related` FROM `shop_products` WHERE `id` IN (?a)", $productIds) as $related) {
            $codes = array_filter(array_map('trim', explode(',', (string)$related)), 'strlen');
            if ($codes) {
                $candidates = array_merge($candidates, array_map('intval', $db->selectCol(
                    "SELECT `id` FROM `shop_products` WHERE `vendor_code` IN (?a) ORDER BY FIELD(`vendor_code`, ?a)", $codes, $codes
                )));
            }
        }
        // По заказам: что покупали вместе (оплаченные и выполненные заказы)
        $candidates = array_merge($candidates, array_map('intval', $db->selectCol(
            "SELECT i2.`product_id`
               FROM `shop_order_items` i1
               JOIN `shop_orders` o ON o.`id` = i1.`shop_order_id` AND o.`status` IN (?a)
               JOIN `shop_order_items` i2 ON i2.`shop_order_id` = i1.`shop_order_id` AND i2.`product_id` NOT IN (?a)
              WHERE i1.`product_id` IN (?a)
              GROUP BY i2.`product_id`
              ORDER BY COUNT(DISTINCT i1.`shop_order_id`) DESC, i2.`product_id` DESC
              LIMIT 50",
            [ShopOrderModel::STATUS_PAID, ShopOrderModel::STATUS_DONE],
            $productIds,
            $productIds
        )));

        $ids = array_values(array_filter(array_unique(array_map('intval', $candidates)), fn($id) => !isset($exclude[$id])));
        if (!$ids) {
            return [];
        }

        // Проверяем всех кандидатов одним запросом: раньше на каждого шло isExists() + чтение строки (до 100 запросов на карточку)
        $rows = array_column(
            $db->select("SELECT `id`, `is_active`, `variant_group` FROM `shop_products` WHERE `id` IN (?a)", $ids),
            null,
            'id'
        );
        $ret = [];
        foreach ($ids as $id) {
            $row = $rows[$id] ?? null;
            if (!$row || !(int)$row['is_active'] || ((string)$row['variant_group'] !== '' && in_array($row['variant_group'], $groups, true))) {
                continue;
            }
            $ret[] = new static($id); // Строки моделей загрузятся одним запросом при обращении к полям
            if (count($ret) >= $limit) {
                break;
            }
        }
        return $ret;
    }

    // ---- Доп. товары/услуги ----

    public function addonGroupIds(): array
    {
        $ids = json_decode((string)$this->addon_group_ids, true);
        return is_array($ids) ? array_values(array_unique(array_filter(array_map('intval', $ids)))) : [];
    }

    public function setAddonGroupIds(array $ids): void
    {
        $this->addon_group_ids = json_encode(array_values(array_unique(array_filter(array_map('intval', $ids)))));
    }

    // Доп. услуги товара из всех его групп, без дублей: [id => ShopAddonModel]
    public function getAddons(): array
    {
        static $cache = [];
        return $cache[(int)$this->id . ':' . $this->addon_group_ids] ??= ShopAddonGroupModel::addonsOfGroups($this->addonGroupIds());
    }

    // Сколько раз доп. услуга входит в позицию: у штучного товара - на каждую штуку, у метражного/весового - одна на позицию
    public function addonQuantity(float $quantity): float
    {
        return $this->isPieceUnit() ? $quantity : 1.0;
    }

    // ---- «Похожие товары» ----

    /**
     * Сначала товары, указанные вручную (similar - артикулы через запятую), потом из той же категории с близкой ценой.
     * Варианты этого же товара не показываем - они и так в переключателе
     */
    public function getSimilar(int $limit = 4): array
    {
        $db        = $this->db;
        $codes     = array_filter(array_map('trim', explode(',', (string)$this->similar)), 'strlen');
        $candidate = $codes ? array_map('intval', $db->selectCol(
            "SELECT `id` FROM ?# WHERE `vendor_code` IN (?a) AND `is_active` = 1 ORDER BY FIELD(`vendor_code`, ?a)", $this->table, $codes, $codes
        )) : [];
        if ($this->category_id) {
            $candidate = array_merge($candidate, array_map('intval', $db->selectCol(
                "SELECT `id` FROM ?# WHERE `category_id` = ?d AND `is_active` = 1 AND `id` <> ?d ORDER BY ABS(`price` - ?) ASC, `sort` ASC LIMIT 30",
                $this->table, $this->category_id, $this->id, $this->price
            )));
        }
        $ids = array_values(array_unique(array_map('intval', $candidate)));
        if (!$ids) {
            return [];
        }

        // Варианты отсеиваем одним запросом, а не чтением модели на каждого кандидата (до 30 лишних SELECT на карточку)
        $variantGroups = array_column(
            $this->db->select("SELECT `id`, `variant_group` FROM ?# WHERE `id` IN (?a) AND `is_active` = 1", $this->table, $ids),
            'variant_group',
            'id'
        );
        $ret = [];
        foreach ($ids as $id) {
            if ($id === (int)$this->id || !array_key_exists($id, $variantGroups)) {
                continue;
            }
            if ($this->variant_group !== '' && $variantGroups[$id] === $this->variant_group) {
                continue;
            }
            $ret[] = new static($id);
            if (count($ret) >= $limit) {
                break;
            }
        }
        return $ret;
    }

    public function hasDiscount(): bool
    {
        return $this->price_old > $this->price;
    }

    public static function findByVendorCode(string $vendor_code): self
    {
        $id = $vendor_code === '' ? null : GetDefaultDb()->selectCell("SELECT `id` FROM `shop_products` WHERE `vendor_code` = ? LIMIT 1", $vendor_code);
        return new self($id ?: null);
    }

    const SORTS = [
        'default'    => 'По умолчанию',
        'price_asc'  => 'Сначала дешевле',
        'price_desc' => 'Сначала дороже',
        'new'        => 'Новинки',
        'rating'     => 'С высоким рейтингом',
    ];
    const SORT_RELEVANCE = 'relevance'; // Только при поиске - по умолчанию

    /**
     * Витрина: поиск, категория, фильтры и сортировка.
     * $f: q, category_id (с подкатегориями), price_min, price_max, in_stock, params, sort, page, per_page
     * price_min, price_max - в рублях; цены товаров - в копейках.
     * Без строки поиска фильтры, сортировка и страницы считаются в SQL: в память попадают только товары текущей страницы.
     * Со строкой поиска релевантность считает ShopSearch (стемминг и исправление раскладки в SQL не сделать).
     */
    public function search(array $f): array
    {
        $q           = trim((string)($f['q'] ?? ''));
        $category_id = (int)($f['category_id'] ?? 0);
        $perPage     = max(1, (int)($f['per_page'] ?? 24));
        $page        = max(1, (int)($f['page'] ?? 1));

        return $q === ''
            ? $this->searchCatalog($f, $category_id, $page, $perPage)
            : $this->searchWithQuery($q, $f, $category_id, $page, $perPage);
    }

    // Каталог без поисковой строки: фасеты и диапазон цен - отдельным лёгким запросом, страница - LIMIT/OFFSET в SQL
    private function searchCatalog(array $f, int $category_id, int $page, int $perPage): array
    {
        $avail = self::availableSql();

        // Фасеты и диапазон цен считаем по товарам категории, до фильтров по цене и характеристикам
        $facetRows = $this->db->select(
            "SELECT `params`, `price` FROM ?# WHERE `is_active` = 1 { AND `category_id` IN (?a) }",
            $this->table,
            $category_id ? ShopCategoryModel::descendantIds($category_id) : DBSIMPLE_SKIP
        );
        foreach ($facetRows as &$row) {
            $row['price']  = (int)$row['price'];
            $row['params'] = json_decode((string)$row['params'], true) ?: [];
        }
        unset($row);

        $facets     = $this->facets($facetRows);
        $prices     = array_column($facetRows, 'price');
        $priceRange = $prices ? [intdiv(min($prices), 100), (int)ceil(max($prices) / 100)] : [0, 0];

        // Характеристики фильтруем только по известным фасетам: имя из адреса не должно ломать выдачу
        $params = [];
        foreach ($facets as $name => $values) {
            $wanted = $f['params'][self::facetKey($name)] ?? null;
            if (is_array($wanted) && $wanted) {
                $params[$name] = array_map('strval', $wanted);
            }
        }
        $min = ($f['price_min'] ?? '') !== '' ? ShopKopecks($f['price_min']) : null; // Рубли из фильтра -> копейки
        $max = ($f['price_max'] ?? '') !== '' ? ShopKopecks($f['price_max']) : null;

        $where = ['`is_active` = 1'];
        $args  = [];
        if ($category_id) {
            $where[] = '`category_id` IN (?a)';
            $args[]  = ShopCategoryModel::descendantIds($category_id);
        }
        if (!empty($f['in_stock'])) {
            $where[] = $avail;
        }
        if ($min !== null) {
            $where[] = '`price` >= ?d';
            $args[]  = $min;
        }
        if ($max !== null) {
            $where[] = '`price` <= ?d';
            $args[]  = $max;
        }
        foreach ($params as $name => $values) {
            // Путь к характеристике подставляем строкой: в DbSimple `?s` - это подзапрос, а не строка
            $where[] = '(JSON_VALID(`params`) AND JSON_UNQUOTE(JSON_EXTRACT(`params`, ' . self::sqlString(self::jsonPath($name)) . ')) IN (?a))';
            $args[]  = $values;
        }
        $whereSql = implode(' AND ', $where);

        $total = (int)$this->db->selectCell("SELECT COUNT(*) FROM ?# WHERE {$whereSql}", $this->table, ...$args);

        $sort = (string)($f['sort'] ?? 'default');
        if (!array_key_exists($sort, self::SORTS)) {
            $sort = 'default';
        }
        $ids = $this->db->selectCol(
            "SELECT `id` FROM ?# WHERE {$whereSql} ORDER BY " . self::sortSql($sort, $avail) . ' LIMIT ?d, ?d',
            $this->table,
            ...array_merge($args, [($page - 1) * $perPage, $perPage])
        );

        return [
            'items'        => $this->idsToModels($ids),
            'total'        => $total,
            'facets'       => $facets,
            'price_range'  => $priceRange,
            'search_query' => '',
        ];
    }

    // Каталог со строкой поиска: стемминг и исправление раскладки считает ShopSearch
    private function searchWithQuery(string $q, array $f, int $category_id, int $page, int $perPage): array
    {
        $rows = $this->db->select(
            "SELECT `id`, `name`, `vendor_code`, `category_id`, `price`, `params`, `sort`, `create_time`, `rating`, `reviews_count`,
                    `track_stock`, `stock`, `unit`, `qty_step`, `qty_min`, `description`
               FROM ?# WHERE `is_active` = 1 { AND `category_id` IN (?a) }",
            $this->table,
            $category_id ? ShopCategoryModel::descendantIds($category_id) : DBSIMPLE_SKIP
        );
        $categories = ShopCategoryModel::rows();
        foreach ($rows as &$row) {
            $row['params']       = json_decode((string)$row['params'], true) ?: [];
            $row['price']        = (int)$row['price'];
            $row['rating']       = (float)$row['rating'];
            // В наличии - как isAvailable(): остатка хватает хотя бы на минимальный заказ (с учётом min_step единицы)
            $row['is_available'] = (int)(!(int)$row['track_stock'] || (float)$row['stock'] >= self::minQuantityBy((string)$row['unit'], (float)$row['qty_step'], (float)$row['qty_min']));
            $row['category']     = isset($categories[(int)$row['category_id']]) ? ShopCategoryModel::pathName((int)$row['category_id']) : '';
        }
        unset($row);

        $found       = ShopSearch::search($q, $rows);
        $searchQuery = $found['query'];
        $rows        = array_values(array_filter($rows, fn($row) => isset($found['scores'][(int)$row['id']])));
        foreach ($rows as &$row) {
            $row['score'] = $found['scores'][(int)$row['id']];
        }
        unset($row);

        $facets     = $this->facets($rows);
        $prices     = array_column($rows, 'price');
        $priceRange = $prices ? [intdiv(min($prices), 100), (int)ceil(max($prices) / 100)] : [0, 0];

        $min    = ($f['price_min'] ?? '') !== '' ? ShopKopecks($f['price_min']) : null;
        $max    = ($f['price_max'] ?? '') !== '' ? ShopKopecks($f['price_max']) : null;
        $params = [];
        foreach ($facets as $name => $values) {
            $wanted = $f['params'][self::facetKey($name)] ?? null;
            if (is_array($wanted) && $wanted) {
                $params[$name] = $wanted;
            }
        }
        $inStock = !empty($f['in_stock']);
        $rows    = array_filter($rows, function ($row) use ($min, $max, $params, $inStock) {
            if ($inStock && !$row['is_available']) {
                return false;
            }
            if (($min !== null && $row['price'] < $min) || ($max !== null && $row['price'] > $max)) {
                return false;
            }
            foreach ($params as $name => $values) {
                if (!in_array((string)($row['params'][$name] ?? ''), array_map('strval', $values), true)) {
                    return false;
                }
            }
            return true;
        });

        // Товары, которых нет в наличии, - всегда в конце
        $sort = $f['sort'] ?? 'default';
        if ($sort === 'default') {
            $sort = self::SORT_RELEVANCE;
        }
        usort($rows, match ($sort) {
            'price_asc'          => fn($a, $b) => [-$a['is_available'], $a['price'], -$a['id']] <=> [-$b['is_available'], $b['price'], -$b['id']],
            'price_desc'         => fn($a, $b) => [-$a['is_available'], $b['price'], -$a['id']] <=> [-$b['is_available'], $a['price'], -$b['id']],
            'new'                => fn($a, $b) => [-$a['is_available'], $b['create_time'], $b['id']] <=> [-$b['is_available'], $a['create_time'], $a['id']],
            'rating'             => fn($a, $b) => [-$a['is_available'], $b['rating'], $b['reviews_count'], $a['sort']] <=> [-$b['is_available'], $a['rating'], $a['reviews_count'], $b['sort']],
            self::SORT_RELEVANCE => fn($a, $b) => [-$a['is_available'], $b['score'] ?? 0, $a['sort'], -$a['id']] <=> [-$b['is_available'], $a['score'] ?? 0, $b['sort'], -$b['id']],
            default              => fn($a, $b) => [-$a['is_available'], $a['sort'], -$a['id']] <=> [-$b['is_available'], $b['sort'], -$b['id']],
        });

        $ids = array_column(array_slice($rows, ($page - 1) * $perPage, $perPage), 'id');

        return [
            'items'        => array_map(fn($id) => new static($id), $ids),
            'total'        => count($rows),
            'facets'       => $facets,
            'price_range'  => $priceRange,
            'search_query' => $searchQuery,
        ];
    }

    // ORDER BY сортировок витрины: сначала товары в наличии (как и раньше, их поднимали в PHP)
    private static function sortSql(string $sort, string $avail): string
    {
        return match ($sort) {
            'price_asc'  => "{$avail} ASC, `price` ASC, `id` DESC",
            'price_desc' => "{$avail} ASC, `price` DESC, `id` DESC",
            'new'        => "{$avail} ASC, `create_time` DESC, `id` DESC",
            'rating'     => "{$avail} ASC, `rating` DESC, `reviews_count` DESC, `sort` ASC",
            default      => "{$avail} ASC, `sort` ASC, `id` DESC",
        };
    }

    /**
     * Условие «товар в наличии» для SQL: остаток не учитывается или его хватает на минимальный заказ.
     * Минимальное количество = max(min_step единицы, шаг, минимум товара) - та же формула, что в minQuantityBy()
     */
    private static function availableSql(): string
    {
        $units   = (array)Config('shop')['units'];
        $default = (string)Config('shop')['default_unit'];

        $cases = '';
        $steps = [];
        foreach ($units as $unit => $conf) {
            $step                 = self::unitMinStep((string)$unit);
            $steps[(string)$unit] = $step;
            $cases               .= ' WHEN ' . self::sqlString((string)$unit) . ' THEN ' . self::sqlNumber($step);
        }
        $defaultStep = $steps[$default] ?? 1.0;

        return "(`track_stock` = 0 OR `stock` >= GREATEST(CASE `unit`{$cases} ELSE " . self::sqlNumber($defaultStep) . ' END, `qty_step`, `qty_min`))';
    }

    // Число для SQL без лишних нулей и без локали (0.5, а не 0,5)
    private static function sqlNumber(float $value): string
    {
        return rtrim(rtrim(number_format($value, 3, '.', ''), '0'), '.') ?: '0';
    }

    // Строка для SQL: экранируем кавычку (значения из конфига, но правило одно для всех)
    private static function sqlString(string $value): string
    {
        return "'" . str_replace("'", "''", $value) . "'";
    }

    // Путь к характеристике в JSON-поле params
    private static function jsonPath(string $name): string
    {
        return '$."' . str_replace(['\\', '"'], ['\\\\', '\\"'], $name) . '"';
    }

    // Подсказки для строки поиска: до $limit товаров по релевантности
    public function suggest(string $q, int $limit = 6): array
    {
        $result = $this->search(['q' => $q, 'per_page' => $limit]);
        return ['items' => $result['items'], 'total' => $result['total'], 'search_query' => $result['search_query']];
    }

    // Ключ характеристики в URL фильтра: Input::clean пропускает в ключах массивов только латиницу, а имена бывают русскими
    public static function facetKey(string $name): string
    {
        return substr(md5($name), 0, 8);
    }

    // Значения характеристик для фильтров: заданные в Config('shop')['filter_params'] или (если null) все, где от 2 до 20 разных значений
    private function facets(array $rows): array
    {
        $values = [];
        foreach ($rows as $row) {
            foreach ($row['params'] as $name => $value) {
                if (is_scalar($value) && (string)$value !== '') {
                    $values[$name][(string)$value] = ($values[$name][(string)$value] ?? 0) + 1;
                }
            }
        }
        $only = Config('shop')['filter_params'] ?? null;
        $ret  = [];
        foreach ($values as $name => $counts) {
            $isWanted = is_array($only) ? in_array($name, $only, true) : (count($counts) >= 2 && count($counts) <= 20);
            if ($isWanted) {
                ksort($counts, SORT_NATURAL | SORT_FLAG_CASE);
                $ret[$name] = $counts;
            }
        }
        return $ret;
    }

    // Активные товары для витрины ($page = 0 - все)
    public function getActiveList(int $page = 0, int $perPage = 0): array
    {
        $ids = $this->db->selectCol(
            "SELECT `id` FROM ?# WHERE `is_active` = 1 ORDER BY `sort` ASC, `id` DESC {LIMIT ?d, ?d}",
            $this->table,
            $page > 0 ? ($page - 1) * $perPage : DBSIMPLE_SKIP,
            $perPage
        );
        return $this->idsToModels($ids);
    }

    public function countActive(): int
    {
        return (int)$this->db->selectCell("SELECT COUNT(*) FROM ?# WHERE `is_active` = 1", $this->table);
    }

    // Активные товары по списку id (ключ - id)
    public function getActiveByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `is_active` = 1 AND `id` IN (?a)", $this->table, array_map('intval', $ids));
        return $this->idsToModels($ids);
    }

    public function getAll(): array
    {
        return $this->idsToModels($this->db->selectCol("SELECT `id` FROM ?# ORDER BY `sort` ASC, `id` DESC", $this->table));
    }

    private function idsToModels(array $ids): array
    {
        $ret = [];
        foreach ($ids as $id) {
            $ret[(int)$id] = new static($id);
        }
        return $ret;
    }
}
