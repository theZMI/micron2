<?php

function _UrlOrigin(string $url): string
{
    $p = parse_url($url);
    if (empty($p['scheme']) || empty($p['host'])) {
        return '';
    }
    return strtolower($p['scheme'] . '://' . $p['host'] . (isset($p['port']) ? ":{$p['port']}" : ''));
}

// Origin текущего сайта (схема + домен + порт)
function SiteOrigin(): string
{
    $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    return strtolower(($isHttps ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? ''));
}

/**
 * Пришёл ли запрос с чужого сайта (защита от CSRF).
 * Смотрим на Sec-Fetch-Site (его шлют все современные браузеры), а если его нет - на Origin и Referer.
 * Если браузер не прислал ничего из этого (прямой заход, curl), то запрос считаем своим.
 */
function IsCrossSiteRequest(): bool
{
    $fetchSite = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '';
    if ($fetchSite !== '') {
        return $fetchSite === 'cross-site';
    }

    $source = $_SERVER['HTTP_ORIGIN'] ?? ($_SERVER['HTTP_REFERER'] ?? '');
    if ($source === '' || $source === 'null') {
        return $source === 'null';
    }

    $origin = _UrlOrigin($source);
    return $origin !== SiteOrigin() && !in_array($origin, Config('cors_allowed_origins'), true);
}

function IsUnsafeHttpMethod(): bool
{
    return !in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD', 'OPTIONS'], true);
}


/**
 * Адрес для редиректа "обратно" (back_url и т.п.): только страницы своего сайта, иначе $default.
 * Защита от open redirect: ссылки вида https://evil.ru, //evil.ru, /\evil.ru не пропускаем
 */
function SafeRedirectUrl($url, string $default): string
{
    $url = trim((string)$url);
    if ($url === '' || preg_match('/[\x00-\x1F\\\\]/', $url)) {
        return $default;
    }
    if ($url[0] === '/') {
        return str_starts_with($url, '//') ? $default : $url;
    }
    return _UrlOrigin($url) === SiteOrigin() ? $url : $default;
}
