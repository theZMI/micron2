<?php

/**
 * Товар магазина
 */
class ShopProductModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'name'             => 'string',
            'vendor_code'      => 'string',
            'price'            => 'float',
            'description'      => 'string',
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
                `description` TEXT DEFAULT NULL,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `sort` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `is_active` (`is_active`, `sort`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_products', $id);
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
