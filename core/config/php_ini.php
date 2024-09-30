<?php

const DEFAULT_TIME_ZONE = 'Europe/Kaliningrad';

Config('phpIni', [
    'error_reporting'        => E_ALL, // Выдавать все ошибки
    'display_errors'         => Env('DEBUG_MODE'), // Выводить ли ошибки в браузер
    'log_errors'             => true,
    'error_log'              => BASEPATH . 'tmp/' . ENV('ERROR_LOG_FILE'),
    'ignore_repeated_errors' => true,
    'memory_limit'           => '32M', // Максимальное количество памяти на выполнение скрипта
    'max_execution_time'     => '15', // Максимальное время выполнения скрипта
    'max_input_time'         => '15' // Время в течении которого скрипту разрешено получать данные
    // "upload_max_filesize" и "post_max_size" - Для изменения размера загружаемых данных (файлов или POST) но задавать нужно через "php.ini | .htaccess | httpd.conf"
]);
