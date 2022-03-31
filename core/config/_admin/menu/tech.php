<?php

$g_config['admin_menu'][] = [
    'link'  => 'javascript:void(0)',
    'name'  => 'Для техников',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link'  => SiteRoot('_admin/error_log'),
            'name'  => 'Лог ошибок',
            'label' => 'Зарегистрированные ошибки на сайте',
        ],
        [
            'link'  => SiteRoot('_admin/admins'),
            'name'  => 'Администраторы',
            'label' => 'Управление администраторами сайта',
            'css'   => '',
            'list'  => [],
        ]
    ],
];
