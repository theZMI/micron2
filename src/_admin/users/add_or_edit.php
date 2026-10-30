<?php

$model       = new UserModel(+Get('id'));
$modelParam  = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $email          = Post('email');
    $full_name      = explode(' ', trim((string)Post('full_name', '')));
    $surname        = $full_name[0] ?? '';
    $first_name     = $full_name[1] ?? '';
    $patronymic     = $full_name[2] ?? '';
    $password       = Post('password');
    $phone          = PhoneFilter(Post('phone'));
    $errs           = [];

    if ($surname === '') {
        $errs[] = "Пожалуйста впишите ФИО сотрудника";
    }
    if (empty($email)) {
        $en_first_name = Translit($first_name);
        $en_patronymic = Translit($patronymic);
        $email        = strtolower( Translit("virtual-email-$surname.".mb_substr($en_first_name, 0, 1).'.'.mb_substr($en_patronymic, 0, 1)) . "@thezmi.pro" );
    }
    if (!$model->isExists() && $model->isEmailBusy($email)) {
        $errs[] = "Пользователь с таким e-mail уже зарегистрирован";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->surname        = $surname;
        $model->first_name     = $first_name;
        $model->patronymic     = $patronymic;
        $model->email          = $email;
        $model->login          = $email;
        $model->status         = UserModel::STATUS_ACTIVE;
        $model->gender         = UserModel::GENDER_UNKNOWN;
        $model->phone          = $phone;

        $isNew = !$model->isExists();
        if ($password) { // Если пароль задан, то устанавливаем его
            $model->pwd_hash = UserModel::hashPassword($password);
        } elseif ($isNew) { // Если пароль не задан, но это мы только-только создаём юзера, то генерим пароль
            $model->pwd_hash = UserModel::hashPassword(UserModel::genPassword());
        }

        $user_id = $model->flush();
        if (!$isNew && $password) {
            $model->revokeAllSessions(); // После смены пароля старые сессии больше не действуют
        }
        UrlRedirect::go(
            SiteRoot("_admin/users&sel_id={$user_id}")
        );
    }
}