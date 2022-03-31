<?php

$g_config['phpIni'] = [
    'error_reporting'    => E_ALL ^ E_DEPRECATED,  // Выдавать все ошибки за исключением нотайсов об устаревшем коде
    'display_errors'     => DEBUG_MODE,            // Выводить ли ошибки в браузер
    'memory_limit'       => '32M',                 // Максимальное количество памяти на выполнение скрипта
    'max_execution_time' => '15',                  // Максимальное время выполнения скрипта
    'max_input_time'     => '15'                   // Время в течении которого скрипту разрешено получать данные
    // "upload_max_filesize" и "post_max_size" - Для изменения размера загружаемых данных (файлов или POST) но задавать нужно через "php.ini | .htaccess | httpd.conf"
];
