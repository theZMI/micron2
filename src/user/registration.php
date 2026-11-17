<?php

RememberAuthBackUrl();
if (IS_USER_AUTH) {
    UrlRedirect::go(AuthRedirectUrl());
}

$msg = '';
if (Post('is_register')) {
    $first_name           = trim((string)Post('first_name'));
    $surname              = trim((string)Post('surname'));
    $email                = trim((string)Post('email'));
    $phone                = PhoneFilter(Post('phone'));
    $phone                = IsValidPhone($phone) ? PhoneFilter($phone) : '';
    $im_agree_with_offers = Post('im_agree_with_offers') === 'on';
    $model                = new UserModel();

    // Такой e-mail уже зарегистрирован - не ругаемся, а отправляем код для входа. Имя и телефон существующего
    // пользователя при этом не меняем: их мог вписать кто угодно, кто знает чужой e-mail
    $existing = $email !== '' ? $model->findOne(['email' => $email]) : null;
    if ($existing && $existing->status === UserModel::STATUS_DELETE) {
        $msg = MsgErr('Аккаунт с этим e-mail удалён. Если это ошибка, напишите нам: ' . SUPPORT_EMAIL);
        return;
    }
    if ($existing) {
        IncludeCom('user/_email_code', [
            'user'    => $existing,
            'purpose' => UserAuthCodeModel::PURPOSE_LOGIN,
            'notice'  => Msg('Этот e-mail уже зарегистрирован - мы отправили на него код для входа'),
        ]);
        ExitCom();
        return;
    }

    $errs = [];
    if ($first_name === '' || $surname === '') {
        $errs[] = "Пожалуйста впишите ваше имя";
    }
    if ($email === '') {
        $errs[] = "Пожалуйста укажите ваш e-mail (он будет использоваться для входа на сайт)";
    } elseif (!IsValidEmail($email)) {
        $errs[] = "Похоже, в e-mail опечатка";
    } elseif ($model->isLoginBusy($email)) { // Логин = e-mail, а такой логин уже задан другому пользователю в админке
        $errs[] = "Этот e-mail нельзя использовать для входа, напишите нам: " . SUPPORT_EMAIL;
    }
    if (!$im_agree_with_offers) {
        $errs[] = "Для работы с сайтом вам необходимо согласиться с соглашениями";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->first_name = $first_name;
        $model->surname    = $surname;
        $model->email      = $email; // Логин станет таким же (см. CommonUserModel::__set)
        $model->phone      = $phone;
        $model->status     = UserModel::STATUS_ACTIVE;
        $model->gender     = UserModel::GENDER_UNKNOWN;

        if ($model->flush()) {
            IncludeCom('user/_email_code', ['user' => $model, 'purpose' => UserAuthCodeModel::PURPOSE_REGISTRATION]);
            ExitCom();
            return;
        } else {
            $msg = MsgErr("Ошибка сохранения данных");
        }
    }
}
