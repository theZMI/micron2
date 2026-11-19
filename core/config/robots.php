<?php

// /robots.txt (src/robots.php). На боевом сайте - эти правила и ссылка на карту сайта с доменом из SITE_DOMAIN;
// при ENV=local и на тестовых адресах (localhost, *.local, *.test, dev/test/staging/qa/preview в имени хоста) - Disallow: /
Config('robots', [
    'disallow' => [
        '/_admin',
        '/_cron',
        '/user/',
        '/api/',
        '/shop/cart',
        '/shop/checkout',
        '/shop/order',
        '/shop/compare',
        '/shop/suggest',
        '/shop/quick_view',
        '/*?*q=',     // Поиск
        '/*?*sort=',  // Сортировка
        '/*?*f%5B',   // Фильтры по характеристикам (f[...])
    ],
    'extra'    => [], // Дополнительные строки как есть, например 'Clean-param: utm_source&utm_medium /'
]);
