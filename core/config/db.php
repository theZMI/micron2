<?php

Config('db_simple', [
    'logDbInfo'  => Env('DEBUG_MODE'), // Логировать ли все запросы к БД? (по умолчанию можно посмотреть в DebugPanel)
    'logDbError' => true, // Логировать запросы в которых произошла ошибка (по умолчанию в dbLogFile складываются)
    'dbLogFile'  => BASEPATH . 'tmp/' . Env('DATABASE_LOG_FILE'),
    'databases'  => [
        'db' => [
            'dsn'        => Env('DATABASE_DSN'),

            // Кеширование запросов к БД:
            // Перед запросом, который нужно закешировать, пишем "-- CACHE: 1h 5m 15" и перенос строки (h/m/число - часы/минуты/секунды)
            // или пользуемся хелпером AddDbCache(). Пример: $db->select(AddDbCache("SELECT * FROM `users`"));
            // Встроенные варианты:
            // MyDataBaseCache::staticCache - кеш на время одного запроса к сайту (убирает повторы одинаковых запросов, ttl не учитывает)
            // MyDataBaseCache::fileCache - файловый кеш с учётом ttl (папка MyDataBaseCache::CACHE_DIR лежит в tmp/ и наружу не отдаётся)
            'pCacheFunc' => ['MyDataBaseCache', 'staticCache'],
        ]
    ]
]);