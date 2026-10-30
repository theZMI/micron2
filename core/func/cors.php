<?php

/**
 * CORS: разрешаем кросс-доменные запросы только с доменов из CORS_ALLOWED_ORIGINS (.env, через запятую).
 * Запросы с того же домена, что и сайт, CORS-заголовков не требуют.
 */
function Cors()
{
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    if ($origin !== '' && in_array(rtrim($origin, '/'), Config('cors_allowed_origins'), true)) {
        header("Access-Control-Allow-Origin: {$origin}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
        header('Vary: Origin');

        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
            header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PATCH, PUT, DELETE");
            header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, API-User-Auth-Token");
        }
    }

    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
        exit(0);
    }
}
