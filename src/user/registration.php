<?php

if (IS_USER_AUTH) {
    UrlRedirect::go(SiteRoot('user/dashboard'));
}

$msg = '';
if (Post('is_register')) {
    $first_name           = Post('first_name');
    $last_name            = Post('last_name');
    $email                = Post('email');
    $phone                = PhoneFilter(Post('phone'));
    $phone                = IsValidPhone($phone) ? PhoneFilter($phone) : '';
    $im_agree_with_offers = Post('im_agree_with_offers') === 'on';
    $model                = new UserModel();

    $errs = [];
    if (empty($first_name) || empty($last_name)) {
        $errs[] = "Пожалуйста впишите ваше имя";
    }
    if (empty($email)) {
        $errs[] = "Пожалуйста укажите ваш e-mail (он будет использоваться для входа на сайт)";
    }
    if (!$im_agree_with_offers) {
        $errs[] = "Для работы с сайтом вам необходимо согласиться с соглашениями";
    }
    if ($model->isEmailBusy($email)) {
        $errs[] = "Пользователь с таким e-mail уже зарегистрирован";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->first_name = $first_name;
        $model->last_name  = $last_name;
        $model->email      = $email;
        $model->login      = $email;
        $model->phone      = $phone;
        $model->pwd_hash   = UserModel::makeHash(UserModel::genPassword());
        $model->status     = UserModel::STATUS_ACTIVE;
        $model->gender     = UserModel::GENDER_UNKNOWN;

        $isSaved = $user_id = $model->flush();
        if ($isSaved) {
            $_POST['is_pwd_recover_order'] = true;
            IncludeCom('user/password_forgot');
            ExitCom();
        } else {
            $msg = MsgErr("Ошибка сохранения данных");
        }
    }
}