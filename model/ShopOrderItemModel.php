<?php

class ShopOrderItemModel extends \Models\ModelExtends
{
    public function createTable()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `shop_order_id` INT DEFAULT NULL,
                `amount` FLOAT DEFAULT NULL,
                `name` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `vendor_code` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_orders', $id);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'currency':
                $model = new ShopOrderModel($this->shop_order_id);
                $ret   = $model->currency;
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function getListByShopOrderID($shop_order_id): array
    {
        $ids = $this->selectCol("SELECT `id` FROM ?# WHERE `shop_order_id` = ?d", $this->table, $shop_order_id);
        $ids = empty($ids) ? [] : $ids;
        return array_map(fn($id) => new self($id), $ids);
    }
}