<?php

Config(['admin_menu', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Отделы',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link' => SiteRoot('_admin/departments'),
            'name' => 'Список',
        ],
    ],
]);