<?php

// Редирект в зависимости от того с www работает сайт или нет

$isHttps    = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
$protocol   = $isHttps ? 'https' : 'http';

// Домен берём из настроек, а не из заголовка Host: подменённый Host уводил такой редирект на чужой сайт.
// И не прогоняем адрес через Input::clean() - он экранирует & в &amp; и ломает параметры в Location
$rootUrl    = rtrim((string)Env('SITE_ROOT_URL'), '/');
$rootHost   = (string)parse_url($rootUrl, PHP_URL_HOST);
$rootScheme = (string)parse_url($rootUrl, PHP_URL_SCHEME);
$rootPort   = parse_url($rootUrl, PHP_URL_PORT);
$protocol   = $rootScheme !== '' ? $rootScheme : $protocol;
$rootHost   = $rootHost !== '' ? $rootHost : (string)($_SERVER['SERVER_NAME'] ?? '');
$bareHost   = stripos($rootHost, 'www.') === 0 ? substr($rootHost, 4) : $rootHost;
$port       = $rootPort ? ":{$rootPort}" : '';

$params     = http_build_query(array_filter($_GET, fn($k) => $k !== 'micron_query', ARRAY_FILTER_USE_KEY));
$path       = implode('/', array_map('rawurlencode', explode('/', (string)GetQuery())));
$requestURI = $path . (strlen($params) ? "?$params" : '');
$requestURI = $path === (string)Config('default_component') ? '/' : "/{$requestURI}";
$curHost    = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
$hasWww     = str_starts_with($curHost, 'www.');
$urlWithWWW    = "{$protocol}://www.{$bareHost}{$port}{$requestURI}";
$urlWithoutWWW = "{$protocol}://{$bareHost}{$port}{$requestURI}";

if (Config('is_www')) {
    if (!$hasWww) {
        UrlRedirect::go($urlWithWWW, 301);
    }
} else {
    if ($hasWww) {
        UrlRedirect::go($urlWithoutWWW, 301);
    }
}
