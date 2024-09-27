<?php

if (IS_USER_AUTH) {
    $backUrl = Get('back_url');
    $backUrl = empty($backUrl) ? SiteRoot('user/dashboard') : str_replace('&amp;', '&', urldecode($backUrl));
    UrlRedirect::go($backUrl);
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
