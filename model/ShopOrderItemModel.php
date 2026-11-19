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
            'parent_item_id'   => 'int', // Доп. услуга - к какой позиции товара
            'product_id'       => 'int',
            'addon_id'         => 'int', // Доп. услуга (ShopAddonModel), 0 - это товар
            'name'             => 'string',
            'vendor_code'      => 'string',
            'price'            => 'int', // В копейках, за единицу, со скидкой по промокоду
            'price_full'       => 'int', // В копейках, за единицу без скидки
            'quantity'         => 'float', // В единицах товара, может быть дробным (2.5 м)
            'unit'             => 'string',
            'is_service'       => 'bool',
            'amount'           => 'int', // В копейках: price × quantity, округлено до копейки
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
                `parent_item_id` INT DEFAULT NULL,
                `product_id` INT DEFAULT NULL,
                `addon_id` INT DEFAULT NULL,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `vendor_code` VARCHAR(100) NOT NULL DEFAULT '',
                `price` BIGINT NOT NULL DEFAULT 0,
                `price_full` BIGINT NOT NULL DEFAULT 0,
                `quantity` DECIMAL(12, 3) NOT NULL DEFAULT 1,
                `unit` VARCHAR(20) NOT NULL DEFAULT 'шт',
                `is_service` TINYINT(1) NOT NULL DEFAULT 0,
                `amount` BIGINT NOT NULL DEFAULT 0,
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

    public function hasDiscount(): bool
    {
        return $this->price_full > $this->price;
    }
}
