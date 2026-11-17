<?php

// Товары: группа вариантов (variant_group), «покупают вместе» вручную (related - артикулы через запятую),
// остаток (stock, учитывается при track_stock = 1). Заказы: отметка, что остаток по отменённому заказу уже возвращён
return function ($db) {
    if (Migrator::tableExists($db, 'shop_products')) {
        $columns = [
            'variant_group' => "VARCHAR(100) NOT NULL DEFAULT '' AFTER `vendor_code`",
            'related'       => "TEXT DEFAULT NULL AFTER `images`",
            'track_stock'   => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `related`",
            'stock'         => "INT NOT NULL DEFAULT 0 AFTER `track_stock`",
        ];
        foreach ($columns as $column => $definition) {
            if (!Migrator::columnExists($db, 'shop_products', $column)) {
                $db->query("ALTER TABLE `shop_products` ADD ?# {$definition}", $column);
            }
        }
        $hasIndex = (bool)$db->selectCell(
            "SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'shop_products' AND index_name = 'variant_group'"
        );
        if (!$hasIndex) {
            $db->query("ALTER TABLE `shop_products` ADD KEY `variant_group` (`variant_group`)");
        }
    }
    if (Migrator::tableExists($db, 'shop_orders') && !Migrator::columnExists($db, 'shop_orders', 'is_stock_returned')) {
        $db->query("ALTER TABLE `shop_orders` ADD `is_stock_returned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`");
    }
};
