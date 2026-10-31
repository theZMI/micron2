<?php

// Домены, которым разрешены кросс-доменные запросы к сайту (CORS). Пример в .env: CORS_ALLOWED_ORIGINS="https://app.example.com,https://example.com"
Config('cors_allowed_origins', array_values(array_filter(array_map(
    fn($origin) => strtolower(rtrim(trim($origin), '/')),
    explode(',', (string)Env('CORS_ALLOWED_ORIGINS', ''))
))));

// IP наших прокси (nginx/балансировщик перед сайтом), через запятую. Только от них принимаем X-Forwarded-For (см. GetClientIP)
Config('trusted_proxies', array_values(array_filter(array_map(
    'trim',
    explode(',', (string)Env('TRUSTED_PROXIES', ''))
))));

// Защита от подбора пароля: после стольких неудачных попыток за период вход блокируется (см. LoginAttemptModel)
Config('login_throttle', [
    'period'       => 15 * 60, // За какой период (в секундах) считаем неудачные попытки
    'max_by_login' => 10,      // Попыток на один логин (с любых IP)
    'max_by_ip'    => 30,      // Попыток с одного IP (по любым логинам)
]);
