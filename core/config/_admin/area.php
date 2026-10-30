<?php

Config('admin_area', [
    'salt'                  => Env('ADMIN_SALT'), // Нужна только для входа со старыми md5-хешами паролей
    'def_login'             => Env('ADMIN_DEFAULT_LOGIN'),
    'def_pwd'               => Env('ADMIN_DEFAULT_PASSWORD'),
    'cookie_token_param'    => 'admin_auth_token', // Имя cookie с токеном сессии
]);