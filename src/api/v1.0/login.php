<?php

$login         = Post('login');
$password_hash = UserModel::makeHash(Post('password'));
$user_id       = (new UserModel())->getIdByLogin($login);
$userModel     = new UserModel($user_id);
$isCorrectAuth = $userModel->pwd_hash === $password_hash;
$response      = $isCorrectAuth ? $password_hash : '';
$apiResponse   = new ApiResponse();

if ($isCorrectAuth) {
    $apiResponse->normal(
        array_merge($userModel->getData(), ['token' => $password_hash])
    );
    return;
}
$apiResponse->error('Неверный логин или пароль');