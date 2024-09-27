<?php

$model       = new UserModel(+Get('id'));
$departments = (new DepartmentModel())->getList();
$modelParam  = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};
$userRoles   = (new UserRoleModel())->getList();

$msg = '';
if (Post('is_set')) {
    $email         = Post('email');
    $first_name    = Post('first_name');
    $last_name     = Post('last_name');
    $password      = Post('password', UserModel::genPassword());
    $department_id = Post('department_id');
    $phone         = PhoneFilter(Post('phone'));
    $role_id       = Post('role_id');
    $errs          = [];

    if (empty($first_name) || empty($last_name)) {
        $errs[] = "Пожалуйста впишите ваше имя";
    }
    if (empty($email)) {
        $errs[] = "Пожалуйста укажите ваш e-mail (он будет использоваться для входа на сайт)";
    }
    if (!$model->isExists() && $model->isEmailBusy($email)) {
        $errs[] = "Пользователь с таким e-mail уже зарегистрирован";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->first_name    = $first_name;
        $model->last_name     = $last_name;
        $model->email         = $email;
        $model->login         = $email;
        $model->pwd_hash      = UserModel::makeHash($password);
        $model->status        = UserModel::STATUS_ACTIVE;
        $model->gender        = UserModel::GENDER_UNKNOWN;
        $model->department_id = $department_id;
        $model->phone         = $phone;
        $model->role_id       = $role_id;

        $user_id = $model->flush();
        UrlRedirect::go(
            SiteRoot("_admin/users&sel_id={$user_id}")
        );
    }
}