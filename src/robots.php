<?php

// robots.txt (маршрут - core/init/router.php, правила - core/config/robots.php)
Config('is_load_in_main_tpl', false);
$config = Config('robots');
$domain = rtrim((string)(Env('SITE_DOMAIN') ?: Env('SITE_ROOT_URL')), '/');

// Закрываем от поиска только то, что точно не боевой сайт: ENV=local или тестовый адрес (dev., test-, staging...).
// Пустой или нестандартный ENV ('prod') - не повод закрыть живой сайт
$host     = strtolower(preg_replace('/:\d+$/', '', (string)($_SERVER['HTTP_HOST'] ?? '')));
$isNoSeo  = IsLocalEnv()
    || in_array($host, ['localhost', '127.0.0.1', '[::1]'], true)
    || preg_match('/(^|[.-])(dev|test|testing|stage|staging|qa|sandbox|preview|demo)([.-]|$)/', $host)
    || preg_match('/\.(local|localhost|test)$/', $host);

$lines = ['User-agent: *'];
if ($isNoSeo) {
    $lines[] = 'Disallow: /'; // Локальная копия или тестовый контур - не индексируем
} else {
    foreach ((array)$config['disallow'] as $path) {
        $lines[] = "Disallow: {$path}";
    }
    $lines   = array_merge($lines, (array)$config['extra']);
    $lines[] = '';
    $lines[] = "Sitemap: {$domain}/sitemap.xml";
}

header('Content-Type: text/plain; charset=utf-8');
die(implode("\n", $lines) . "\n");
