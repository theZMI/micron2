<?php

Config('user_model', [
    'salt'                  => Env('USER_SALT'), // Нужна только для входа со старыми md5-хешами паролей
    'cookie_token_param'    => 'user_auth_token',    // Имя cookie с токеном сессии
    'pwd_recover_time'      => 15 * 60,          // Сколько секунд действует код из письма
    'pwd_recover_attempts'  => 5,                // Сколько попыток ввода кода даётся на один отправленный код
    'pwd_recover_resend'    => 60,               // Не чаще чем раз в столько секунд можно запросить новый код
    'without_auth'          => [                 // Страницы без авторизации, но по пути, где есть авторизация (в нашем случае /user)
        'user/login',
        'user/registration',
        'user/password_forgot',
    ],
]);
