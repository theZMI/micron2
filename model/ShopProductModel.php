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

    public function hasDiscount(): bool
    {
        return $this->price_old > $this->price;
    }

    public static function findByVendorCode(string $vendor_code): self
    {
        $id = $vendor_code === '' ? null : GetDefaultDb()->selectCell("SELECT `id` FROM `shop_products` WHERE `vendor_code` = ? LIMIT 1", $vendor_code);
        return new self($id ?: null);
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
