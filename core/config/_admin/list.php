<?php

Config('admin_list', [
    // Какие страницы не показывать в списке страниц
    'exceptions'   => [
        '_dev',
        '_autoload',
    ],
    // Нужно ли делать backup-ы
    'with_backups' => true
]);