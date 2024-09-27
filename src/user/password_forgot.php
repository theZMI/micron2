<?php

if (IS_USER_AUTH) {
    UrlRedirect::go(SiteRoot('user/dashboard'));
}

$msg              = '';
$isCheckCodeBlock = Post('is_code_check_block');

// Это заказ на восстановление пароля
if (Post('is_pwd_recover_order')) {
    $email = Post('email');

    $errs = [];
    if (empty($email)) {
        $errs[] = "Пожалуйста введите ваш e-mail";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $user_id = (new UserModel())->getIdByEmail($email);
        if (empty($user_id)) {
            $msg = MsgErr(L("Не найден пользователь с данным e-mail"));
        } else {
            $code_id    = (new UserPwdRecoverModel())->generate($user_id);
            $pwdRecover = new UserPwdRecoverModel($code_id);
            $code       = $pwdRecover->code;
            $isCodeSend = (new EmailNotificator())->send(
                new UserModel($user_id),
                "<p>Для завершения регистрации, пожалуйста, введите данный код на сайте:</p>" .
                "<p style='padding: 30px 0; text-align: center; font-size: 40px; letter-spacing: 5px;'>{$code}</p>",
                "Завершение регистрации"
            );

            if (!$isCodeSend) {
                $msg = MsgErr("Ошибка отправки кода");
            } else {
                $isCheckCodeBlock = true;
            }
        }
    }

    unset($_POST['is_pwd_recover']);
}

// Проверка введённого кода
if (Post('is_check_pwd_recover_code')) {
    $email       = Post('email');
    $user_code   = Post('user_code');
    $isValidCode = (new UserPwdRecoverModel())->isValidCode($user_code, $email);

    if ($isValidCode) {
        $userModel                    = new UserModel(
            (new UserModel())->getIdByEmail($email)
        );
        $userModel->is_email_verified = true;
        $userModel->login($userModel->email, $userModel->pwd_hash);
        UrlRedirect::go(SiteRoot('user/dashboard'));
    }
    $msg = MsgErr("Неверный код");
}