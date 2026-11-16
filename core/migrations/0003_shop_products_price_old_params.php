<?php

// Товары: цена до скидки, характеристики (JSON) и индекс по артикулу для импорта из таблицы (ShopProductImporter)
return function ($db) {
    if (!Migrator::tableExists($db, 'shop_products')) {
        return; // Таблица создастся сразу с этими полями (ShopProductModel::createTable)
    }
    if (!Migrator::columnExists($db, 'shop_products', 'price_old')) {
        $db->query("ALTER TABLE `shop_products` ADD `price_old` DECIMAL(12, 2) NOT NULL DEFAULT 0 AFTER `price`");
    }
    if (!Migrator::columnExists($db, 'shop_products', 'params')) {
        $db->query("ALTER TABLE `shop_products` ADD `params` TEXT DEFAULT NULL AFTER `description`");
    }
    $hasIndex = (bool)$db->selectCell(
        "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'shop_products' AND index_name = 'vendor_code'"
    );
    if (!$hasIndex) {
        $db->query("ALTER TABLE `shop_products` ADD KEY `vendor_code` (`vendor_code`)");
    }
};
