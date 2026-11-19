<?php

// Доп. товары/услуги к товару (подарочная упаковка, продлённая гарантия...): у товара - группы доп. услуг (addon_group_ids),
// в заказе - позиции доп. услуг привязаны к позиции товара (parent_item_id). Таблицы shop_addons и shop_addon_groups модели создают сами
return function ($db) {
    if (Migrator::tableExists($db, 'shop_products') && !Migrator::columnExists($db, 'shop_products', 'addon_group_ids')) {
        $db->query("ALTER TABLE `shop_products` ADD `addon_group_ids` TEXT DEFAULT NULL AFTER `similar`");
    }
    if (Migrator::tableExists($db, 'shop_order_items')) {
        $columns = [
            'parent_item_id' => "INT DEFAULT NULL AFTER `shop_order_id`", // Позиция товара, к которой эта доп. услуга
            'addon_id'       => "INT DEFAULT NULL AFTER `product_id`",
            'is_service'     => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `unit`", // Для чека: услуга, а не товар
        ];
        foreach ($columns as $column => $definition) {
            if (!Migrator::columnExists($db, 'shop_order_items', $column)) {
                $db->query("ALTER TABLE `shop_order_items` ADD ?# {$definition}", $column);
            }
        }
    }
};
