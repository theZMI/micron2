<?php

// Куда вернуть пользователя после входа или регистрации: login/registration?back_url=... запоминают адрес в сессии
function RememberAuthBackUrl(): void
{
    $url = str_replace('&amp;', '&', urldecode((string)Get('back_url', '')));
    $url = SafeRedirectUrl($url, ''); // Только страницы своего сайта
    if ($url !== '') {
        SessionManager::set('auth_back_url', $url);
    }
}

function AuthRedirectUrl(): string
{
    $url = (string)SessionManager::get('auth_back_url');
    if ($url !== '') {
        SessionManager::delete('auth_back_url');
    }
    return $url !== '' ? $url : SiteRoot('user/dashboard');
}
