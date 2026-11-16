<?php

// Запрос из JS (fetch/jQuery с заголовком X-Requested-With) - отвечаем JSON-ом, а не страницей
function IsAjaxRequest(): bool
{
    return strtolower((string)($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
}
