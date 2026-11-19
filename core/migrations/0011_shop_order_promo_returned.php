<?php

// Флаг «использование промокода по отменённому заказу вернули». Нужен, чтобы при отмене заказа вернуть промокод ровно один
// раз (а при возврате заказа из отмены - снова его израсходовать)
return function ($db) {
    if (!Migrator::tableExists($db, 'shop_orders')) {
        return;
    }
    if (!Migrator::columnExists($db, 'shop_orders', 'is_promo_returned')) {
        $db->query("ALTER TABLE `shop_orders` ADD COLUMN `is_promo_returned` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_stock_returned`");
    }
};
