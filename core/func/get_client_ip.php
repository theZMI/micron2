<?php

/**
 * IP посетителя.
 * Заголовки X-Forwarded-For и пр. может прислать кто угодно, поэтому верим им, только если запрос пришёл
 * от нашего прокси (балансировщик, nginx перед apache) из списка TRUSTED_PROXIES в .env
 */
function GetClientIP(): string
{
    $remote = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    if (!in_array($remote, Config('trusted_proxies') ?: [], true)) {
        return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '';
    }

    // Идём по цепочке прокси справа налево: первый адрес, который не наш прокси, - это клиент
    $chain = array_map('trim', explode(',', (string)($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')));
    foreach (array_reverse($chain) as $ip) {
        if (filter_var($ip, FILTER_VALIDATE_IP) && !in_array($ip, Config('trusted_proxies'), true)) {
            return $ip;
        }
    }
    return $remote;
}
