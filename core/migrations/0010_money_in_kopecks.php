<?php

// Деньги храним целыми числами в копейках (BIGINT) вместо DECIMAL(12,2) в рублях: никакой арифметики с дробями ни в БД, ни в PHP.
// Проценты (промокоды «percent», доп. услуги «percent») - в сотых долях процента: 7.5% -> 750. Всё это - умножение на 100
return function ($db) {
    $columns = [
        'shop_products'    => ['price', 'price_old'],
        'shop_orders'      => ['total_amount', 'items_amount', 'discount_amount', 'delivery_price'],
        'shop_order_items' => ['price', 'price_full', 'amount'],
        'payments'         => ['amount'],
        'shop_promo_codes' => ['value', 'min_amount'],
        'shop_addons'      => ['price_value'],
    ];
    foreach ($columns as $table => $fields) {
        if (!Migrator::tableExists($db, $table)) {
            continue; // Таблицу создаст модель - сразу в копейках
        }
        foreach ($fields as $field) {
            $type = $db->selectRow(
                "SELECT `DATA_TYPE` AS `type`, `IS_NULLABLE` AS `nullable` FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?",
                $table,
                $field
            );
            if (!$type || strtolower($type['type']) !== 'decimal') {
                continue; // Колонки нет или она уже в копейках
            }
            $null = $type['nullable'] === 'YES' ? 'DEFAULT NULL' : 'NOT NULL DEFAULT 0';
            // Сначала расширяем, чтобы ×100 не переполнило DECIMAL(12,2), потом - в целое
            $db->query("ALTER TABLE ?# MODIFY ?# DECIMAL(16, 2) {$null}", $table, $field);
            $db->query("UPDATE ?# SET ?# = ROUND(?# * 100) WHERE ?# IS NOT NULL", $table, $field, $field, $field);
            $db->query("ALTER TABLE ?# MODIFY ?# BIGINT {$null}", $table, $field);
        }
    }
};
