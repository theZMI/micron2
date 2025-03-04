<?php

// Редирект в зависимости от того с www работает сайт или нет

$serverName    = stripos($_SERVER['SERVER_NAME'], 'www.') === 0 ? substr($_SERVER['SERVER_NAME'], 4) : $_SERVER['SERVER_NAME'];
$params        = http_build_query(array_filter($_GET, fn($k) => $k !== 'micron_query', ARRAY_FILTER_USE_KEY));
$requestURI    = GetQuery() . (strlen($params) ? "?$params" : '');
$requestURI    = $requestURI === Config('default_component') ? '/' : "/{$requestURI}";
$isHttps       = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
$protocol      = $isHttps ? 'https' : 'http';
$input         = Input::getInstance();
$curUrl        = $input->clean("{$protocol}://" . $_SERVER['SERVER_NAME'] . "{$requestURI}", 0);
$urlWithWWW    = $input->clean("{$protocol}://www.{$serverName}{$requestURI}", 0);
$urlWithoutWWW = $input->clean("{$protocol}://{$serverName}{$requestURI}", 0);
$hasWww        = str_starts_with($curUrl, "{$protocol}://www");

if (Config('is_www')) {
    if (!$hasWww) {
        UrlRedirect::go($urlWithWWW, 301);
    }
} else {
    if ($hasWww) {
        UrlRedirect::go($urlWithoutWWW, 301);
    }
}
