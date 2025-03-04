<?php

Config('db_simple', [
    'logDbInfo'  => Env('DEBUG_MODE'), // Логировать ли все запросы к БД? (по умолчанию можно посмотреть в DebugPanel)
    'logDbError' => true, // Логировать запросы в которых произошла ошибка (по умолчанию в dbLogFile складываются)
    'dbLogFile'  => BASEPATH . 'tmp/' . Env('DATABASE_LOG_FILE'),
    'databases'  => [
        'db' => [
            'dsn'        => Env('DATABASE_DSN'),
            'pCacheFunc' => ['MyDataBaseCache', 'Cache'], // Перед запросом который в кеш отправиться нужно написать "-- CACHE: 1h 5m 15" затем ENTER. Здесь h/m/цифра это часы/минут/секунды соответственно
        ]
    ]
]);