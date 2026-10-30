<?php

// Домены, которым разрешены кросс-доменные запросы к сайту (CORS). Пример в .env: CORS_ALLOWED_ORIGINS="https://app.example.com,https://example.com"
Config('cors_allowed_origins', array_values(array_filter(array_map(
    fn($origin) => strtolower(rtrim(trim($origin), '/')),
    explode(',', (string)Env('CORS_ALLOWED_ORIGINS', ''))
))));
