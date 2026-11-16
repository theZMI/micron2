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
        'link'  => 'javascript:void(0)',
        'name'  => 'Примеры',
        'label' => '',
        'css'   => '',
        'list'  => [
            [
                'link' => SiteRoot('examples/engine'),
                'name' => 'Немножко о функциях движка',
            ],
            'divider',
            [
                'link' => SiteRoot('examples/api'),
                'name' => 'Примеры API',
            ],
            [
                'link' => SiteRoot('examples/ajax_form'),
                'name' => 'Ajax-формы',
            ],
            [
                'link' => SiteRoot('examples/ajax_upload'),
                'name' => 'Ajax загрука файлов',
            ],
            [
                'link' => SiteRoot('examples/ekko_lightbox'),
                'name' => 'Модальное окно',
            ],
            [
                'link' => SiteRoot('examples/force_download'),
                'name' => 'Вызов загрузки файла через скрипт',
            ],
            [
                'link' => SiteRoot('examples/gen_db_error'),
                'name' => 'Запрос к БД с ошибкой (тест логгирования)',
            ],
            [
                'link' => SiteRoot('examples/gen_php_error'),
                'name' => 'Тест ошибки в php-коде',
            ],
            [
                'link' => SiteRoot('examples/datepicker'),
                'name' => 'Выбор даты',
            ],
            [
                'link' => SiteRoot('examples/timepicker'),
                'name' => 'Выбор времени',
            ],
            [
                'link' => SiteRoot('examples/masks'),
                'name' => 'IMask - форматирование данных в input-ах',
            ],
            [
                'link' => SiteRoot('examples/formats'),
                'name' => 'Форматы вывода данных (даты, суммы, время)',
            ],
            [
                'link' => SiteRoot('examples/yandex_address_input'),
                'name' => 'Ввод гео-адреса через yandex',
            ],
            [
                'link' => SiteRoot('examples/geo'),
                'name' => 'Geo ф-ии (географические)',
            ],
            [
                'link' => SiteRoot('examples/magicsuggest'),
                'name' => 'Прокаченный select',
            ],
            [
                'link' => SiteRoot('examples/messages'),
                'name' => 'MsgOk, MsgErr... - сообщения в формах',
            ],
            [
                'link' => SiteRoot('examples/micron_cache'),
                'name' => 'Кеширование',
            ],
            [
                'link' => SiteRoot('examples/mockup_models'),
                'name' => 'Модели-заглушки (модели с тестовыми данными)',
            ],
            [
                'link' => SiteRoot('examples/pagesbar'),
                'name' => 'Постраничный бар',
            ],
            [
                'link' => SiteRoot('examples/pwd_shower'),
                'name' => 'Показать / скрыть пароль',
            ],
            [
                'link' => SiteRoot('examples/router'),
                'name' => 'Роутинг',
            ],
            [
                'link' => SiteRoot('examples/session_manager'),
                'name' => 'Работа с сессией',
            ],
            [
                'link' => SiteRoot('examples/syntaxhighliner'),
                'name' => 'Выделение кода',
            ],
            [
                'link' => SiteRoot('examples/url_redirect'),
                'name' => 'Для редиректов',
            ],
            [
                'link' => SiteRoot('examples/vue3_app'),
                'name' => 'Vue3 приложение',
            ],
            [
                'link' => SiteRoot('examples/vue3_default_app'),
                'name' => 'Vue3 приложение с дефолтным шаблоном',
            ],
            [
                'link' => SiteRoot('examples/round_to'),
                'name' => 'Округление чисел/времени',
            ],
            [
                'link' => SiteRoot('examples/qr'),
                'name' => 'QR-коды',
            ],
            [
                'link' => SiteRoot('examples/live_filter'),
                'name' => 'Мультифильтры (как в XL)',
            ],
            [
                'link' => SiteRoot('examples/xl'),
                'name' => 'Работа с XL файлами',
            ],
            [
                'link' => SiteRoot('examples/mobile_detector'),
                'name' => 'Mobile detect',
            ],
            [
                'link' => SiteRoot('examples/simple_captcha'),
                'name' => 'Simple captcha',
            ],
            [
                'link' => SiteRoot('examples/number_format_js'),
                'name' => 'Работа с js ф-ей number_format()',
            ],
        ],
    ],
    [
        'link' => SiteRoot(),
        'name' => 'О проекте',
    ],
    [
        'link' => SiteRoot('contacts'),
        'name' => 'Связь',
    ],
];

$countMessages = $g_user->countNotifies();
$shopState     = ShopHeaderState();
$badge         = fn($count, $attr) => '<span class="nav-badge' . ($count ? '' : ' d-none') . '" ' . $attr . '>' . $count . '</span>';
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
            'name'    => '<i class="bi bi-person"></i><span class="nav-icon-label">Кабинет</span>' . $badge($countMessages, ''),
            'html'    => 'data-bs-toggle="offcanvas" role="button" aria-controls="offcanvasUserMenu"',
            'linkCss' => 'nav-icon',
            'label'   => 'Личный кабинет',
        ]
        : [
            'link'    => SiteRoot('user/login&back_url=' . urlencode(str_replace('&amp;', '&', GetCurUrl()))),
            'name'    => '<i class="bi bi-person"></i><span class="nav-login-text">Войти</span>',
            'linkCss' => 'nav-icon nav-login',
            'label'   => 'Вход и регистрация',
        ],
];

// На телефоне корзина и избранное видны рядом с кнопкой меню, а не внутри него
$mobileIcons = '<a href="' . SiteRoot('shop/favorites') . '" class="nav-icon" aria-label="Избранное"><i class="bi bi-heart"></i>' . $badge($shopState['favorites_count'], 'data-favorites-count') . '</a>'
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
    $userMenu[] = [
        'link' => SiteRoot('user/password_forgot'),
        'name' => 'Забыли пароль?'
    ];
}