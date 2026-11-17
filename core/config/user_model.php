<?php

Config('user_model', [
    'cookie_token_param'    => 'user_auth_token',    // Имя cookie с токеном сессии
    // Вход только по одноразовому коду из письма (паролей у пользователей нет), см. UserAuthCodeModel
    'auth_code_ttl'         => 15 * 60,          // Сколько секунд действует код из письма
    'auth_code_attempts'    => 5,                // Сколько попыток ввода даётся на один отправленный код
    'auth_code_resend'      => 60,               // Не чаще чем раз в столько секунд можно запросить новый код
    'without_auth'          => [                 // Страницы без авторизации, но по пути, где есть авторизация (в нашем случае /user)
        'user/login',
        'user/registration',
        'user/password_forgot', // Только редирект на user/login (старые ссылки)
    ],
]);
