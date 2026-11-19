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

/**
 * Кнопка изменяющего действия (удалить, очистить, включить...) - формой POST вместо ссылки.
 * Ссылку-действие может дёрнуть кто угодно: prefetch браузера, расширение, сканер, чужой сайт через <img>.
 * POST так не вызвать, а от чужих сайтов его защищает проверка Sec-Fetch-Site в CommonMiddleware
 *
 * @param string $action  значение поля a
 * @param array  $params  остальные поля формы (id и т.п.)
 * @param string $confirm текст подтверждения ('' - без подтверждения)
 * @param string $class   классы кнопки
 * @param string $inner   содержимое кнопки (иконка или текст)
 */
function PostActionButton(string $action, array $params = [], string $confirm = '', string $class = 'btn btn-sm btn-danger rounded-pill', string $inner = '<i class="bi bi-trash3-fill"></i>', string $title = 'Удалить'): string
{
    $hidden = '<input type="hidden" name="a" value="' . htmlspecialchars($action, ENT_QUOTES) . '">';
    foreach ($params as $name => $value) {
        $hidden .= '<input type="hidden" name="' . htmlspecialchars((string)$name, ENT_QUOTES)
            . '" value="' . htmlspecialchars((string)$value, ENT_QUOTES) . '">';
    }
    $onSubmit = $confirm !== ''
        ? ' onsubmit="return confirm(' . htmlspecialchars(json_encode($confirm, JSON_UNESCAPED_UNICODE), ENT_QUOTES) . ')"'
        : '';

    return '<form action="' . htmlspecialchars(GetCurUrl(), ENT_QUOTES) . '" method="post" class="d-inline"' . $onSubmit . '>'
        . $hidden
        . '<button type="submit" class="' . $class . '" title="' . htmlspecialchars($title, ENT_QUOTES) . '">' . $inner . '</button>'
        . '</form>';
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
