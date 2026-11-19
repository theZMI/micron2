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
            'link' => SiteRoot('_admin/shop/categories'),
            'name' => 'Категории',
        ],
        [
            'link' => SiteRoot('_admin/shop/addons'),
            'name' => 'Доп. товары и услуги',
        ],
        [
            'link' => SiteRoot('_admin/shop/addon_groups'),
            'name' => 'Группы доп. услуг',
        ],
        [
            'link' => SiteRoot('_admin/shop/reviews'),
            'name' => 'Отзывы',
        ],
        [
            'link' => SiteRoot('_admin/shop/promo_codes'),
            'name' => 'Промокоды',
        ],
        [
            'link' => SiteRoot('_admin/shop/import'),
            'name' => 'Импорт товаров',
        ],
    ],
]);
