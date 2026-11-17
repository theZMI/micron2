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
            'price'            => 'float',
            'price_old'        => 'float', // Цена до скидки (0 - нет скидки)
            'description'      => 'string',
            'params'           => 'string',
            'images'           => 'string', // JSON: имена картинок (ShopImage), первая - главная
            'related'          => 'string', // «Покупают вместе» вручную: артикулы через запятую
            'track_stock'      => 'bool', // Учитывать остаток
            'stock'            => 'int',
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
                `price` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `price_old` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `description` TEXT DEFAULT NULL,
                `params` TEXT DEFAULT NULL,
                `images` TEXT DEFAULT NULL,
                `related` TEXT DEFAULT NULL,
                `track_stock` TINYINT(1) NOT NULL DEFAULT 0,
                `stock` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `is_active` (`is_active`, `sort`),
                KEY `vendor_code` (`vendor_code`),
                KEY `variant_group` (`variant_group`),
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

    // ---- Наличие ----

    public function isAvailable(): bool
    {
        return !$this->track_stock || $this->stock > 0;
    }

    // Сколько можно положить в корзину
    public function maxQuantity(): int
    {
        $max = (int)Config('shop')['max_quantity'];
        return $this->track_stock ? max(0, min($max, $this->stock)) : $max;
    }

    // Подпись о наличии (только если остаток учитывается): ['text' => ..., 'css' => 'in|low|out']
    public function availability(): ?array
    {
        if (!$this->track_stock) {
            return null;
        }
        if ($this->stock <= 0) {
            return ['text' => 'Нет в наличии', 'css' => 'out'];
        }
        return $this->stock <= (int)Config('shop')['low_stock']
            ? ['text' => 'Осталось ' . $this->stock . ' шт.', 'css' => 'low']
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

        $ret = [];
        foreach (array_unique($candidates) as $id) {
            if (isset($exclude[$id])) {
                continue;
            }
            $product = new static($id);
            if (!$product->isExists() || !$product->is_active || ($product->variant_group !== '' && in_array($product->variant_group, $groups, true))) {
                continue;
            }
            $ret[] = $product;
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
    ];

    /**
     * Витрина: поиск, фильтры и сортировка.
     * $f: q (строка поиска), price_min, price_max, in_stock (только в наличии), params ([facetKey('Цвет') => ['синий', ...]]), sort, page, per_page
     * Возвращает: items (модели текущей страницы), total, facets (['Цвет' => ['синий' => кол-во]]), price_range ([min, max])
     * Характеристики фильтруются в PHP - для каталогов до нескольких тысяч товаров этого достаточно
     */
    public function search(array $f): array
    {
        $q    = trim((string)($f['q'] ?? ''));
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $rows = $this->db->select(
            "SELECT `id`, `price`, `params`, `sort`, `create_time`, (`track_stock` = 0 OR `stock` > 0) AS `is_available` FROM ?# WHERE `is_active` = 1 { AND (`name` LIKE ? OR `vendor_code` LIKE ? OR `description` LIKE ?) }",
            $this->table,
            $q === '' ? DBSIMPLE_SKIP : $like,
            $like,
            $like
        );
        foreach ($rows as &$row) {
            $row['params'] = json_decode((string)$row['params'], true) ?: [];
            $row['price']        = (float)$row['price'];
            $row['is_available'] = (int)$row['is_available'];
        }
        unset($row);

        $facets     = $this->facets($rows);
        $prices     = array_column($rows, 'price');
        $priceRange = $prices ? [floor(min($prices)), ceil(max($prices))] : [0, 0];

        $min    = is_numeric($f['price_min'] ?? null) ? (float)$f['price_min'] : null;
        $max    = is_numeric($f['price_max'] ?? null) ? (float)$f['price_max'] : null;
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
        usort($rows, match ($sort) {
            'price_asc'  => fn($a, $b) => [-$a['is_available'], $a['price'], -$a['id']] <=> [-$b['is_available'], $b['price'], -$b['id']],
            'price_desc' => fn($a, $b) => [-$a['is_available'], $b['price'], -$a['id']] <=> [-$b['is_available'], $a['price'], -$b['id']],
            'new'        => fn($a, $b) => [-$a['is_available'], $b['create_time'], $b['id']] <=> [-$b['is_available'], $a['create_time'], $a['id']],
            default      => fn($a, $b) => [-$a['is_available'], $a['sort'], -$a['id']] <=> [-$b['is_available'], $b['sort'], -$b['id']],
        });

        $perPage = max(1, (int)($f['per_page'] ?? 24));
        $page    = max(1, (int)($f['page'] ?? 1));
        $ids     = array_column(array_slice($rows, ($page - 1) * $perPage, $perPage), 'id');
        return [
            'items'       => array_map(fn($id) => new static($id), $ids),
            'total'       => count($rows),
            'facets'      => $facets,
            'price_range' => $priceRange,
        ];
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
