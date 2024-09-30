<?php

Config('admin_area', [
    'salt'                  => Env('ADMIN_SALT'),
    'def_login'             => Env('ADMIN_DEFAULT_LOGIN'),
    'def_pwd'               => Env('ADMIN_DEFAULT_PASSWORD'),
    'cookie_login_param'    => 'admin_auth_login',
    'cookie_password_param' => 'admin_auth_pwd_hash',
]);