<?php

// Магазин (model/Shop*Model.php, lib/ShopCart.php, src/shop/*, src/_admin/shop/*)
Config('shop', [
    'currency'        => 'RUB', // Валюта цен и заказов (её же получает платёжная система)
    'currency_sign'   => '₽',
    'max_quantity'    => 999, // Максимум одного товара в корзине
    'manager_emails'  => array_filter(array_map('trim', explode(',', (string)Env('SHOP_MANAGER_EMAILS', '')))), // Кому писать об оплаченных заказах
    'catalog_per_page' => 24,
    'unpaid_order_ttl_hours' => 72, // Через сколько часов неоплаченный заказ отменяется (_cron/shop_cancel_unpaid) и товар возвращается на склад
    'low_stock'        => 5, // «Осталось N шт.» при остатке не больше этого (если остаток учитывается)
    'variant_image_params' => ['Цвет', 'Color', 'Вариант'], // Характеристики вариантов, которые переключаются картинками; остальные (размер...) - кнопками
    'filter_params'    => null, // Характеристики для фильтров каталога, например ['Цвет', 'Размер']. null - все, у которых от 2 до 20 разных значений
    'import'           => [ // Импорт товаров из таблицы (lib/ShopProductImporter.php, _admin/shop/import, _cron/shop_import)
        'google_sheet_url' => (string)Env('SHOP_IMPORT_GOOGLE_SHEET_URL', ''), // Ссылка на Google-таблицу с доступом «Все, у кого есть ссылка»
        'hide_missing'     => true, // Снимать с продажи товары, которых нет в таблице
    ],
]);
