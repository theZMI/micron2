<?php

Config(['admin_menu', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Смены',
    'label' => '',
    'css'   => '',
    'list'  => [
        [
            'link'  => SiteRoot('_admin/dir_shifts'),
            'name'  => 'Смены',
        ],
    ]
]);