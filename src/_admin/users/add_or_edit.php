<?php

$model       = new UserModel(+Get('id'));
$modelParam  = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $email          = Post('email');
    $full_name      = explode(' ', Post('full_name', ''));
    $surname        = $full_name[0] ?? '';
    $first_name     = $full_name[1] ?? '';
    $patronymic     = $full_name[2] ?? '';
    $password       = Post('password');
    $phone          = PhoneFilter(Post('phone'));
    $errs           = [];

    if (empty($full_name)) {
        $errs[] = "Пожалуйста впишите ФИО сотрудника";
    }
    if (empty($email)) {
        $en_first_name = Translit($first_name);
        $en_patronymic = Translit($patronymic);
        $email        = strtolower( Translit("virtual-email-$surname.".$en_first_name[0].'.'.$en_patronymic[0]) . "@thezmi.pro" );
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

        if ($password) { // Если пароль задан, то устанавливаем его
            $model->pwd_hash = UserModel::makeHash($password);
        } elseif (!$model->isExists()) { // Если пароль не задан, но это мы только-только создаём юзера, то генерим пароль
            $model->pwd_hash = UserModel::makeHash(UserModel::genPassword());
        }

        $user_id = $model->flush();
        UrlRedirect::go(
            SiteRoot("_admin/users&sel_id={$user_id}")
        );
    }
}