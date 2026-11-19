<?php

// Товары: «Похожие товары» вручную (similar - артикулы через запятую) и картинки из импорта (image_sources - откуда скачаны)
return function ($db) {
    if (!Migrator::tableExists($db, 'shop_products')) {
        return; // Таблицу создаст модель - сразу с новыми колонками
    }
    $columns = [
        'similar'       => "TEXT DEFAULT NULL AFTER `related`",
        'image_sources' => "TEXT DEFAULT NULL AFTER `images`",
    ];
    foreach ($columns as $column => $definition) {
        if (!Migrator::columnExists($db, 'shop_products', $column)) {
            $db->query("ALTER TABLE `shop_products` ADD ?# {$definition}", $column);
        }
    }
};
