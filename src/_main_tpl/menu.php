<?php

$logo = [
    'logo'  => '<img src="' . Root('i/image/logo.png') . '" alt="" />',
    'link'  => SiteRoot(),
    'title' => 'Micron',
    'css'   => 'site-logo me-5'
];

$menu = [
    [
        'link' => SiteRoot(),
        'name' => 'Что это?',
    ],
    [
        'link'  => 'javascript:void(0)',
        'name'  => 'Примеры',
        'label' => '',
        'css'   => '',
        'list'  => [
            [
                'link' => SiteRoot('about_us'),
                'name' => 'Кратко о нас',
            ],
            'divider',
            [
                'link' => SiteRoot('contacts'),
                'name' => 'Контакты',
            ],
        ],
    ],
    [
        'link' => SiteRoot('contacts'),
        'name' => 'Связь',
    ],
];

$countMessages = 1;
$rightMenu     = [
    [
        'link'    => '#offcanvasUserMenu',
        'name'    => '<i class="bi bi-person-circle d-none d-lg-inline"></i><span class="d-inline d-lg-none">Личный кабинет</span>' .
                     (
                         $countMessages
                         ? '<span class="position-absolute top-50 translate-middle p-1 badge rounded-pill bg-danger">+' . $countMessages . '<span class="visually-hidden">Новых сообщений</span></span>'
                         : ''
                     ),
        'html'    => 'data-bs-toggle="offcanvas" href="#offcanvasUserMenu" role="button" aria-controls="offcanvasUserMenu"',
        'linkCss' => 'position-relative',
    ]
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