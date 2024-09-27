<?php

$admin_id = Get('id');
$isAdd    = empty($admin_id);
$isEdit   = !$isAdd;
$admin    = new AdminModel($admin_id);

if ($admin_id && !$admin->isExists()) {
    trigger_error("Invalid admin id.", E_USER_ERROR);
}

$login = trim((string)Post('login', $admin_id ? $admin->login : ''));
$pwd   = Post('pwd');
$pwd2  = Post('pwd2');
$name  = trim((string)Post('name', $admin_id ? $admin->name : ''));
$desc  = trim((string)Post('desc', $admin_id ? $admin->desc : ''));
$email = trim((string)Post('email', $admin_id ? $admin->email : ''));
$phone = PhoneFilter(trim((string)Post('phone', $admin_id ? $admin->phone : '')));

$msg = '';
if (Post('is_apply')) {
    $errs = [];
    if (empty($login)) {
        $errs[] = "Логин не может быть пустым";
    }
    if (!$admin_id && empty($pwd)) {
        $errs[] = "Пароль не может быть пустым";
    }
    if ($pwd != $pwd2) {
        $errs[] = "Ошибка подтверждения пароля";
    }
    if (!$admin_id && $admin->isLoginBusy($login)) {
        $errs[] = "Данный логин уже используется";
    }
    if ($admin_id && $admin->login != $login && $admin->isLoginBusy($login)) {
        $errs[] = "Данный логин уже используется";
    }

    if (empty($errs)) {
        $admin->login = $login;
        if (!empty($pwd)) {
            $admin->pwd_hash = $admin->makeHash($pwd);
        }
        $admin->name  = $name;
        $admin->desc  = $desc;
        $admin->email = $email;
        $admin->phone = $phone;

        $id = $admin->flush();
        if ($id) {
            $msg   = MsgOk("Операция успешно выполнена");
            $_POST = [];
        } else {
            $errs[] = "Ошибка регистрации";
        }
    }

    if (!empty($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    }
}
