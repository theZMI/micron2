<?php

FillArrayFromPhpInput($_POST);

$login         = Post('login');
$password_hash = Post('password_hash');
$apiResponse   = new ApiResponse();
$isCorrectAuth = (new UserModel())->isCorrectAuth($login, $password_hash);
$apiResponse->normal($isCorrectAuth);