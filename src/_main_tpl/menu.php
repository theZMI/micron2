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
                'link' => SiteRoot('examples/engine'),
                'name' => 'Немножко о функциях движка',
            ],
            'divider',
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
                'link' => SiteRoot('examples/vue_app'),
                'name' => 'Vue3 приложение',
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