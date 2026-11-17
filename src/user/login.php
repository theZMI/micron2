<?php

// Вход без пароля: логин (по умолчанию это e-mail) → код из письма → вход
RememberAuthBackUrl();
if (IS_USER_AUTH) {
    $backUrl = str_replace('&amp;', '&', urldecode((string)Get('back_url')));
    UrlRedirect::go(SafeRedirectUrl($backUrl, SiteRoot('user/dashboard'))); // Только на страницы своего сайта
}

// Проверка введённого кода или повторная отправка
if (Post('is_check_email_code') || Post('is_resend_email_code')) {
    IncludeCom('user/_email_code');
    ExitCom();
    return;
}

$msg = '';
if (Post('is_login')) {
    $login = trim((string)Post('login'));
    $user  = UserModel::findActiveByLogin($login);

    if ($login === '') {
        $msg = MsgErr('Пожалуйста укажите ваш логин (e-mail)');
    } elseif (!$user) { // Искать пользователя имеет смысл, только если логин задан
        $msg = MsgErr('Не найден пользователь с таким логином');
    } else {
        IncludeCom('user/_email_code', ['user' => $user, 'purpose' => UserAuthCodeModel::PURPOSE_LOGIN]);
        ExitCom();
        return;
    }
}
