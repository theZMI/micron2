<?php

// Магазин: категории товаров, единицы измерения (дробное количество), рейтинг по отзывам,
// доставка, способ оплаты и промокод в заказе. Новые таблицы (shop_categories, shop_reviews, shop_promo_codes,
// shop_stock_subscriptions) модели создают сами
return function ($db) {
    $addColumns = function (string $table, array $columns) use ($db) {
        if (!Migrator::tableExists($db, $table)) {
            return; // Таблицу создаст модель - сразу с новыми колонками
        }
        foreach ($columns as $column => $definition) {
            if (!Migrator::columnExists($db, $table, $column)) {
                $db->query("ALTER TABLE ?# ADD ?# {$definition}", $table, $column);
            }
        }
    };
    $addIndex = function (string $table, string $index, string $columns) use ($db) {
        if (!Migrator::tableExists($db, $table)) {
            return;
        }
        $has = (bool)$db->selectCell(
            "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?",
            $table,
            $index
        );
        if (!$has) {
            $db->query("ALTER TABLE ?# ADD KEY ?# ({$columns})", $table, $index);
        }
    };

    $addColumns('shop_products', [
        'category_id'   => "INT DEFAULT NULL AFTER `variant_group`",
        'unit'          => "VARCHAR(20) NOT NULL DEFAULT 'шт' AFTER `price_old`",
        'qty_step'      => "DECIMAL(10, 3) NOT NULL DEFAULT 1 AFTER `unit`",
        'qty_min'       => "DECIMAL(10, 3) NOT NULL DEFAULT 1 AFTER `qty_step`",
        'rating'        => "DECIMAL(3, 2) NOT NULL DEFAULT 0 AFTER `stock`",
        'reviews_count' => "INT NOT NULL DEFAULT 0 AFTER `rating`",
    ]);
    $addIndex('shop_products', 'category_id', '`category_id`');
    if (Migrator::tableExists($db, 'shop_products')) {
        $db->query("ALTER TABLE `shop_products` MODIFY `stock` DECIMAL(12, 3) NOT NULL DEFAULT 0");
    }

    $addColumns('shop_order_items', [
        'price_full' => "DECIMAL(12, 2) NOT NULL DEFAULT 0 AFTER `price`", // Цена до скидки по промокоду
        'unit'       => "VARCHAR(20) NOT NULL DEFAULT 'шт' AFTER `quantity`",
    ]);
    if (Migrator::tableExists($db, 'shop_order_items')) {
        $db->query("ALTER TABLE `shop_order_items` MODIFY `quantity` DECIMAL(12, 3) NOT NULL DEFAULT 1");
        $db->query("UPDATE `shop_order_items` SET `price_full` = `price` WHERE `price_full` = 0");
    }

    $addColumns('shop_orders', [
        'items_amount'     => "DECIMAL(12, 2) NOT NULL DEFAULT 0 AFTER `total_amount`",
        'discount_amount'  => "DECIMAL(12, 2) NOT NULL DEFAULT 0 AFTER `items_amount`",
        'promo_code'       => "VARCHAR(50) NOT NULL DEFAULT '' AFTER `discount_amount`",
        'delivery_method'  => "VARCHAR(30) NOT NULL DEFAULT '' AFTER `promo_code`",
        'delivery_name'    => "VARCHAR(255) NOT NULL DEFAULT '' AFTER `delivery_method`",
        'delivery_price'   => "DECIMAL(12, 2) NOT NULL DEFAULT 0 AFTER `delivery_name`",
        'delivery_address' => "TEXT DEFAULT NULL AFTER `delivery_price`",
        'payment_method'   => "VARCHAR(20) NOT NULL DEFAULT 'online' AFTER `delivery_address`",
    ]);
    if (Migrator::tableExists($db, 'shop_orders')) {
        $db->query("UPDATE `shop_orders` SET `items_amount` = `total_amount` WHERE `items_amount` = 0");
    }
};
