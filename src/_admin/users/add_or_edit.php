<?php

$model       = new UserModel(intval(Get('id')));
$modelParam  = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $email      = trim((string)Post('email'));
    $login      = trim((string)Post('login'));
    $full_name  = explode(' ', trim((string)Post('full_name', '')));
    $surname    = $full_name[0] ?? '';
    $first_name = $full_name[1] ?? '';
    $patronymic = $full_name[2] ?? '';
    $phone      = PhoneFilter(Post('phone'));
    $errs       = [];

    // Чужой ли это e-mail / логин (у самого редактируемого пользователя совпадение - не ошибка)
    $busyByOther = fn(?CommonUserModel $found) => $found && (!$model->isExists() || (int)$found->id !== (int)$model->id);

    if ($surname === '') {
        $errs[] = "Пожалуйста впишите ФИО";
    }
    if ($email === '') {
        $errs[] = "Укажите e-mail: на него приходит код для входа";
    } elseif (!IsValidEmail($email)) {
        $errs[] = "Похоже, в e-mail опечатка";
    } elseif ($busyByOther($model->findOne(['email' => $email]))) {
        $errs[] = "Пользователь с таким e-mail уже есть";
    }
    if ($busyByOther($model->findOne(['login' => $login !== '' ? $login : $email]))) {
        $errs[] = "Такой логин уже занят";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $isNew             = !$model->isExists();
        $model->surname    = $surname;
        $model->first_name = $first_name;
        $model->patronymic = $patronymic;
        $model->email      = $email;
        $model->login      = $login !== '' ? $login : $email; // Логин по умолчанию равен e-mail
        $model->phone      = $phone;
        if ($isNew) {
            $model->status = UserModel::STATUS_ACTIVE;
            $model->gender = UserModel::GENDER_UNKNOWN;
        }

        $user_id = $model->flush();
        UrlRedirect::go(
            SiteRoot("_admin/users&sel_id={$user_id}")
        );
    }
}
