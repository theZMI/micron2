<?php

if (IS_USER_AUTH) {
    $backUrl = str_replace('&amp;', '&', urldecode((string)Get('back_url')));
    UrlRedirect::go(SafeRedirectUrl($backUrl, SiteRoot('user/dashboard'))); // Только на страницы своего сайта
}

$msg = '';
if (Post('is_login')) {
    $email = Post('email');
    $model = new UserModel();

    $errs = [];
    if (empty($email)) {
        $errs[] = "Пожалуйста укажите ваш e-mail";
    }
    if (!$model->isEmailBusy($email)) {
        $errs[] = "Не найден пользователь с данным e-mail";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $_POST['is_pwd_recover_order'] = true;
        IncludeCom('user/password_forgot');
        ExitCom();
    }
}
