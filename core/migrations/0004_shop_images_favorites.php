<?php

// Картинки товаров (ShopImage) и избранное пользователей (ShopFavorites)
return function ($db) {
    if (Migrator::tableExists($db, 'shop_products') && !Migrator::columnExists($db, 'shop_products', 'images')) {
        $db->query("ALTER TABLE `shop_products` ADD `images` TEXT DEFAULT NULL AFTER `params`");
    }
    $db->query(
        "CREATE TABLE IF NOT EXISTS `shop_favorites` (
            `user_id` INT NOT NULL,
            `product_id` INT NOT NULL,
            `create_time` INT NOT NULL,
            PRIMARY KEY (`user_id`, `product_id`)
        ) ENGINE = InnoDB"
    );
};
