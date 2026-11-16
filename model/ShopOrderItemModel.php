<?php

/**
 * Позиция заказа. Название, артикул и цена копируются из товара на момент заказа, чтобы правка товара не меняла старые заказы
 */
class ShopOrderItemModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'shop_order_id'    => 'int',
            'product_id'       => 'int',
            'name'             => 'string',
            'vendor_code'      => 'string',
            'price'            => 'float',
            'quantity'         => 'int',
            'amount'           => 'float', // price * quantity
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `shop_order_id` INT NOT NULL,
                `product_id` INT DEFAULT NULL,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `vendor_code` VARCHAR(100) NOT NULL DEFAULT '',
                `price` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `quantity` INT NOT NULL DEFAULT 1,
                `amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `shop_order_id` (`shop_order_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_order_items', $id);
    }

    public function getListByShopOrderId(int $shop_order_id): array
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `shop_order_id` = ?d ORDER BY `id`", $this->table, $shop_order_id);
        return array_map(fn($id) => new static($id), $ids);
    }
}
