<?php

// Магазин (model/Shop*Model.php, lib/ShopCart.php, src/shop/*, src/_admin/shop/*)
Config('shop', [
    'currency'        => 'RUB', // Валюта цен и заказов (её же получает платёжная система)
    'currency_sign'   => '₽',
    'max_quantity'    => 999, // Максимум одного товара в корзине
    'manager_emails'  => array_filter(array_map('trim', explode(',', (string)Env('SHOP_MANAGER_EMAILS', '')))), // Кому писать об оплаченных заказах
    'catalog_per_page' => 24,
    'import'           => [ // Импорт товаров из таблицы (lib/ShopProductImporter.php, _admin/shop/import, _cron/shop_import)
        'google_sheet_url' => (string)Env('SHOP_IMPORT_GOOGLE_SHEET_URL', ''), // Ссылка на Google-таблицу с доступом «Все, у кого есть ссылка»
        'hide_missing'     => true, // Снимать с продажи товары, которых нет в таблице
    ],
]);
