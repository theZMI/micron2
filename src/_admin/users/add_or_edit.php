<?php

$model       = new UserModel(+Get('id'));
$departments = (new DepartmentModel())->getList();
$modelParam  = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $email         = Post('email');
    $surname       = Post('surname');
    $first_name    = Post('first_name');
    $patronymic    = Post('patronymic');
    $password      = Post('password', UserModel::genPassword());
    $department_id = +Post('department_id');
    $phone         = PhoneFilter(Post('phone'));
    $role          = +Post('role');
    $errs          = [];

    if (empty($first_name) || empty($patronymic) || empty($surname)) {
        $errs[] = "Пожалуйста впишите ФИО сотрудника";
    }
    if (empty($email)) {
        $en_first_name = Translit($first_name);
        $en_patronymic = Translit($patronymic);
        $email        = strtolower( Translit("worker-$surname.".$en_first_name[0].'.'.$en_patronymic[0]) . "@kkosa.ru" );
    }
    if (!$model->isExists() && $model->isEmailBusy($email)) {
        $errs[] = "Пользователь с таким e-mail уже зарегистрирован";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->surname       = $surname;
        $model->first_name    = $first_name;
        $model->patronymic    = $patronymic;
        $model->email         = $email;
        $model->login         = $email;
        $model->pwd_hash      = UserModel::makeHash($password);
        $model->status        = UserModel::STATUS_ACTIVE;
        $model->gender        = UserModel::GENDER_UNKNOWN;
        $model->department_id = $department_id;
        $model->phone         = $phone;
        $model->role          = $role;

        $user_id = $model->flush();
        UrlRedirect::go(
            SiteRoot("_admin/users&sel_id={$user_id}")
        );
    }
}