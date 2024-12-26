<?php

$newUserData = Post('userData', []);
$newPassword = Post('newPassword');

FileSys::writeFile(BASEPATH.'tmp_save_users', json_encode($newUserData), true);
FileSys::writeFile(BASEPATH.'tmp_save_passwords', json_encode(['user_id' => $newUserData['id'], 'newPassword' => $newPassword]), true);

if ($newPassword) {
    $g_user->pwd_hash = UserModel::makeHash($newPassword);
}
foreach ($newUserData as $k => $v) {
    if ($g_user->$k !== $v) {
        $g_user->$k = $v;
    }
}
$g_user->flush();

(new ApiResponse())->normal($g_user->getData());