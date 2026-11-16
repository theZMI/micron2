<?php

// Магазин (model/Shop*Model.php, lib/ShopCart.php, src/shop/*, src/_admin/shop/*)
Config('shop', [
    'currency'        => 'RUB', // Валюта цен и заказов (её же получает платёжная система)
    'currency_sign'   => '₽',
    'max_quantity'    => 999, // Максимум одного товара в корзине
    'manager_emails'  => array_filter(array_map('trim', explode(',', (string)Env('SHOP_MANAGER_EMAILS', '')))), // Кому писать об оплаченных заказах
    'catalog_per_page' => 24,
]);
