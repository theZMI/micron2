<?php

// Редирект в зависимости от того с www работает сайт или нет
$serverName = stripos($_SERVER['SERVER_NAME'], 'www.') === 0 ? substr($_SERVER['SERVER_NAME'], 4) : $_SERVER['SERVER_NAME'];

$newGet = $_GET;
unset($newGet['micron_query']);

$params     = http_build_query($newGet);
$requestURI = GetQuery() . (strlen($params) ? "?$params" : '');
$requestURI = $requestURI === Config('defaultComponent') ? '/' : "/{$requestURI}";

$isHttps       = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
$protocol      = $isHttps ? 'https' : 'http';
$input         = Input::getInstance();
$curUrl        = $input->clean("{$protocol}://" . $_SERVER['SERVER_NAME'] . "{$requestURI}", 0);
$urlWithWWW    = $input->clean("{$protocol}://www.{$serverName}{$requestURI}", 0);
$urlWithoutWWW = $input->clean("{$protocol}://{$serverName}{$requestURI}", 0);

if (Config('is_www')) {
    if (strpos($curUrl, "{$protocol}://www") !== 0) {
        UrlRedirect::go($urlWithWWW, 301);
    }
} else {
    if (strpos($curUrl, "{$protocol}://www") === 0) {
        UrlRedirect::go($urlWithoutWWW, 301);
    }
}
