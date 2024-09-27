<?php

if (IS_ADMIN_AUTH) {
    UrlRedirect::go(SiteRoot('_admin/home'));
}

$msg = '';
if (Post('is_login')) {
    $login = Post('login');
    $pwd   = Post('password');
    $errs  = [];

    if (empty($login)) {
        $errs[] = "Впишите логин";
    }
    if (empty($pwd)) {
        $errs[] = "Впишите пароль";
    }

    if (!count($errs)) {
        $isLogin = $g_admin->login($login, $g_admin->makeHash($pwd));
        $isLogin ? UrlRedirect::go(SiteRoot('_admin/home')) : ($errs[] = "Неверный логин или пароль");
    }

    $msg = MsgErr(implode('<br>', $errs));
}