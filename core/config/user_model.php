<?php

Config('user_model', [
    'salt'                  => 'KNHAS12asdi9fnxcuaB!3---HdC%^&*sdbg',   // Соль для хеша пароля
    'cookie_login_param'    => 'auth_login',                            // Имя cookie в которой будет храниться логин пользователя
    'cookie_password_param' => 'auth_pwd_hash',                         // Имя cookie для хеш-а на пароль пользователя
    'pwd_recover_time'      => 60 * 60,                                 // Как долго по присланной человеку ссылке можно будет восстановить пароль (секунды)
    'without_auth'          => [                                        // Страницы без авторизации, но по пути, где есть авторизация (в нашем случае /user)
        'user/login',
        'user/registration',
        'user/password_forgot',
    ],
]);