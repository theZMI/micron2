<?php

Config(['admin_menu', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Пользователи',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link' => SiteRoot('_admin/users'),
            'name' => 'Список',
        ],
    ],
]);