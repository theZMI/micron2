<?php

Config(['admin_menu_right', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Для техников',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link' => SiteRoot('_admin/error_log'),
            'name' => 'Лог ошибок',
        ],
        [
            'link' => SiteRoot('_admin/admins'),
            'name' => 'Администраторы',
        ],
        [
            'link' => SiteRoot('_admin/extrapacker_info'),
            'name' => 'Информация по склеенным extraPacker-ом файлам',
        ],
        [
            'link' => SiteRoot('_admin/toggle_site_maintenance'),
            'name' => 'Закрыть/открыть сайт',
        ],
    ],
]);