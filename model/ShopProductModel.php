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
            'price'            => 'float',
            'price_old'        => 'float', // Цена до скидки (0 - нет скидки)
            'description'      => 'string',
            'params'           => 'string',
            'images'           => 'string', // JSON: имена картинок (ShopImage), первая - главная
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
                `price` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `price_old` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `description` TEXT DEFAULT NULL,
                `params` TEXT DEFAULT NULL,
                `images` TEXT DEFAULT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `is_active` (`is_active`, `sort`),
                KEY `vendor_code` (`vendor_code`),
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
     * $f: q (строка поиска), price_min, price_max, params ([facetKey('Цвет') => ['синий', ...]]), sort, page, per_page
     * Возвращает: items (модели текущей страницы), total, facets (['Цвет' => ['синий' => кол-во]]), price_range ([min, max])
     * Характеристики фильтруются в PHP - для каталогов до нескольких тысяч товаров этого достаточно
     */
    public function search(array $f): array
    {
        $q    = trim((string)($f['q'] ?? ''));
        $like = '%' . addcslashes($q, '%_\\') . '%';
        $rows = $this->db->select(
            "SELECT `id`, `price`, `params`, `sort`, `create_time` FROM ?# WHERE `is_active` = 1 { AND (`name` LIKE ? OR `vendor_code` LIKE ? OR `description` LIKE ?) }",
            $this->table,
            $q === '' ? DBSIMPLE_SKIP : $like,
            $like,
            $like
        );
        foreach ($rows as &$row) {
            $row['params'] = json_decode((string)$row['params'], true) ?: [];
            $row['price']  = (float)$row['price'];
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
        $rows   = array_filter($rows, function ($row) use ($min, $max, $params) {
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

        $sort = $f['sort'] ?? 'default';
        usort($rows, match ($sort) {
            'price_asc'  => fn($a, $b) => [$a['price'], -$a['id']] <=> [$b['price'], -$b['id']],
            'price_desc' => fn($a, $b) => [$b['price'], -$a['id']] <=> [$a['price'], -$b['id']],
            'new'        => fn($a, $b) => [$b['create_time'], $b['id']] <=> [$a['create_time'], $a['id']],
            default      => fn($a, $b) => [$a['sort'], -$a['id']] <=> [$b['sort'], -$b['id']],
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
