<?php

Config('user_model', [
    'salt'                  => Env('USER_SALT'), // Соль для хеша пароля
    'cookie_login_param'    => 'user_auth_login',     // Имя cookie в которой будет храниться логин пользователя
    'cookie_password_param' => 'user_auth_pwd_hash',  // Имя cookie для хеш-а на пароль пользователя
    'pwd_recover_time'      => 60 * 60,          // Как долго по присланной человеку ссылке можно будет восстановить пароль (секунды)
    'without_auth'          => [                 // Страницы без авторизации, но по пути, где есть авторизация (в нашем случае /user)
        'user/login',
        'user/registration',
        'user/password_forgot',
    ],
]);
