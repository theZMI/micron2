<?php

Config(['admin_menu', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Магазин',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link' => SiteRoot('_admin/shop/orders'),
            'name' => 'Заказы',
        ],
        [
            'link' => SiteRoot('_admin/shop/products'),
            'name' => 'Товары',
        ],
        [
            'link' => SiteRoot('_admin/shop/import'),
            'name' => 'Импорт товаров',
        ],
    ],
]);
