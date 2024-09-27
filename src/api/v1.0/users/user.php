<?php

FillArrayFromPhpInput($_POST);
//$_POST['userData'] = ['first_name' => 'Michail test 111'];
$newUserData = Post('userData', []);
$newPassword = Post('newPassword');

if ($newPassword) {
    $g_user->pwd_hash = UserModel::makeHash($newPassword);
}
foreach ($newUserData as $k => $v) {
    if ($g_user->$k !== $v) {
        $g_user->$k = $v;
    }
}
$curUserData = $g_user->getData();
(new ApiResponse())->normal($curUserData);