<?php

$logo = [
    'logo'  => '<span class="site-logo-text">Micron</span>',
    'link'  => SiteRoot(),
    'title' => 'Micron',
    'css'   => 'site-logo me-5'
];

$menu = [
    [
        'link' => SiteRoot('shop'),
        'name' => 'Каталог',
    ],
    [
        'link' => SiteRoot('examples'), // Список - core/config/examples.php
        'name' => 'Компоненты',
    ],
    [
        'link' => SiteRoot('manual'),
        'name' => 'Документация',
    ],
    [
        'link' => SiteRoot('contacts'),
        'name' => 'Связь',
    ],
];

$countMessages = $g_user->countNotifies();
$shopState     = ShopHeaderState();
$badge         = fn($count, $attr) => '<span class="nav-badge' . ($count ? '' : ' d-none') . '" ' . $attr . '>' . $count . '</span>';
// Аватар вместо иконки человечка: у пользователя - свой (если загружен), у гостя и без аватара - стандартный
$avatarUrl     = IS_USER_AUTH && (string)$g_user->avatar !== '' ? $g_user->avatar_url : Root('i/image/avatars/default_80x80.png');
$avatarImg     = '<img src="' . $avatarUrl . '" class="nav-avatar" width="28" height="28" alt="">';
$rightMenu     = [
    [
        'link'    => SiteRoot('shop/favorites'),
        'name'    => '<i class="bi bi-heart"></i><span class="nav-icon-label">Избранное</span>' . $badge($shopState['favorites_count'], 'data-favorites-count'),
        'linkCss' => 'nav-icon',
        'label'   => 'Избранное',
    ],
    [
        'link'    => SiteRoot('shop/cart'),
        'name'    => '<i class="bi bi-bag"></i>' . $badge($shopState['cart_count'], 'data-cart-count') .
                     '<span class="nav-cart-total' . ($shopState['cart_count'] ? '' : ' d-none') . '" data-cart-total>' . $shopState['cart_total_text'] . '</span>' .
                     '<span class="nav-icon-label">Корзина</span>',
        'linkCss' => 'nav-icon nav-cart',
        'label'   => 'Корзина',
    ],
    IS_USER_AUTH
        ? [
            'link'    => '#offcanvasUserMenu',
            'name'    => $avatarImg . '<span class="nav-icon-label">Кабинет</span>' . $badge($countMessages, ''),
            'html'    => 'data-bs-toggle="offcanvas" role="button" aria-controls="offcanvasUserMenu"',
            'linkCss' => 'nav-icon',
            'label'   => 'Личный кабинет',
        ]
        : [
            'link'    => SiteRoot('user/login&back_url=' . urlencode(str_replace('&amp;', '&', GetCurUrl()))),
            'name'    => $avatarImg . '<span class="nav-icon-label">Войти</span>',
            'linkCss' => 'nav-icon nav-login',
            'label'   => 'Вход и регистрация',
        ],
];

// На телефоне корзина и избранное видны рядом с кнопкой меню, а не внутри него
ob_start();
IncludeCom('_main_tpl/theme_switch');
$mobileIcons = ob_get_clean() . '<a href="' . SiteRoot('shop/favorites') . '" class="nav-icon" aria-label="Избранное"><i class="bi bi-heart"></i>' . $badge($shopState['favorites_count'], 'data-favorites-count') . '</a>'
             . '<a href="' . SiteRoot('shop/cart') . '" class="nav-icon" aria-label="Корзина"><i class="bi bi-bag"></i>' . $badge($shopState['cart_count'], 'data-cart-count') . '</a>';

$userMenu = [];
if (IS_USER_AUTH) {
    $userMenu[] = [
        'link' => SiteRoot('user/dashboard'),
        'name' => 'Главная кабинета'
    ];
    $userMenu[] = [
        'link' => SiteRoot('user/orders'),
        'name' => 'Мои заказы'
    ];
    $userMenu[] = [
        'link' => SiteRoot('shop/favorites'),
        'name' => 'Избранное'
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
}