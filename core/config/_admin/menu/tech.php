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
            'link' => SiteRoot('_tests/email_checker'),
            'name' => 'Отправить тестовое письмо',
        ],
        [
            'link' => SiteRoot('_cron/free_space_checker'),
            'name' => 'Проверить свободное место на сервере',
        ],
        [
            'link' => SiteRoot('_cron/db_backup'),
            'name' => 'Сделать backup БД',
        ],
        [
            'link' => SiteRoot('pay_system/_cron_check'),
            'name' => 'Проверить waiting-платежи',
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