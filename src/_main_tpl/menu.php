<?php

$logo = [
    'logo'  => '<img src="' . Root('i/image/logo.png') . '" alt="" />',
    'link'  => SiteRoot(),
    'title' => 'Micron',
    'css'   => 'site-logo me-5'
];

$menu = [
    [
        'link'  => 'javascript:void(0)',
        'name'  => 'Кратко о нас',
        'label' => '',
        'css'   => '',
        'list'  => [
            [
                'link' => SiteRoot(),
                'name' => 'Кратко о нас',
            ],
            [
                'link' => SiteRoot('requisites'),
                'name' => 'Реквизиты',
            ],
            'divider',
            [
                'link' => SiteRoot('contacts'),
                'name' => 'Контакты',
            ],
        ],
    ],
    [
        'link' => SiteRoot('shop/catalog'),
        'name' => 'Каталог товаров',
    ],
    [
        'link' => SiteRoot('blog'),
        'name' => 'Блог',
    ],
];

$countMessages = 1;
$rightMenu     = [
    [
        'link'    => '#offcanvasUserMenu',
        'name'    => '<i class="bi bi-person-circle d-none d-lg-inline"></i><span class="d-inline d-lg-none">Личный кабинет</span>' .
            ($countMessages ? '<span class="position-absolute top-50 translate-middle p-1 badge rounded-pill bg-danger">+' . $countMessages . '<span class="visually-hidden">Новых сообщений</span></span>' : ''),
        'html'    => 'data-bs-toggle="offcanvas" href="#offcanvasUserMenu" role="button" aria-controls="offcanvasUserMenu"',
        'linkCss' => 'position-relative',
    ],
    [
        'link'    => SiteRoot('shop/likes'),
        'name'    => '<i class="bi bi-suit-heart-fill d-none d-lg-inline"></i><span class="d-inline d-lg-none">Понравилось</span>',
        'linkCss' => 'position-relative',
    ],
    [
        'link'    => SiteRoot('shop/cart'),
        'name'    => '<i class="bi bi-basket2-fill d-none d-lg-inline"></i><span class="d-inline d-lg-none">Корзина</span>',
        'linkCss' => 'position-relative'
    ],
];

$userMenu = [];
if (IS_USER_AUTH) {
    $userMenu[] = [
        'link' => SiteRoot('user/dashboard'),
        'name' => 'Главная кабинета'
    ];
    $userMenu[] = [
        'link' => SiteRoot('user/settings'),
        'name' => 'Настройки'
    ];
    $userMenu[] = [
        'link' => SiteRoot('user/logout'),
        'name' => 'Выход'
    ];
} else {
    $userMenu[] = [
        'link' => SiteRoot('user/login'),
        'name' => 'Вход'
    ];
    $userMenu[] = [
        'link' => SiteRoot('user/registration'),
        'name' => 'Регистрация'
    ];
    $userMenu[] = [
        'link' => SiteRoot('user/password_forgot'),
        'name' => 'Забыли пароль?'
    ];
}