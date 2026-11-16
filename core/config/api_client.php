<?php

// Клиент внешнего API (lib/ApiClient.php, model/ApiModel.php)
Config('api_client', [
    'root_url'   => (string)Env('API_CLIENT_ROOT_URL', ''), // Например: https://example.com/api/v1.0/
    'auth_token' => (string)Env('API_CLIENT_AUTH_TOKEN', ''), // Передаётся в заголовке API-User-Auth-Token
    'timeout'    => 30, // Секунд на весь запрос
    'ssl_verify' => !IsLocalEnv(), // Локально можно ходить на сервер с самоподписанным сертификатом
    'log_file'   => (string)Env('API_CLIENT_LOG_FILE', ''), // Лог изменяющих запросов и ошибок в tmp/, пусто - не логировать
]);
