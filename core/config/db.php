<?php

Config('dbSimple', [
    'logDbInfo' => DEBUG_MODE, // Логгировать ли все запросы к БД? (по умолчанию можно посмотреть в DebugPanel)
    'logDbError' => true, // Логгировать запросы в которых произошла ошибка (по умолчанию в dbLogFile складываются)
    'dbLogFile' => BASEPATH . 'tmp/db_errors.log',

    'databases' => [
        'db' => [
            'dsn' => EnvConfig::DATABASE_DSN,
            'pCacheFunc' => ['MyDataBaseCache', 'Cache'], // Перед запросом который в кеш отправиться нужно написать "-- CACHE: 1h 5m 15" затем ENTER. Здесь h/m/цифра это часы/минут/секунды соответственно
        ]
    ]
]);
