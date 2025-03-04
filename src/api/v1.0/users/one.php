<?php

ini_set('memory_limit', '512M');

$model         = isset($id) ? new UserModel($id) : $g_user;
$newPassword   = Post('new_password');
$canEditFields = [
    'email',
    'phone',
    'avatar',
    'device_id',
    'telegram_login',
    'telegram_chat_id',
    'first_name',
    'surname',
    'patronymic',
    'gender',
    'birthday_date',
    'notification_by_phone',
    'notification_by_email',
    'notification_by_telegram',
    'job_title',
];

if (Post('is_set')) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, $canEditFields)) {
            continue;
        }
        if ($k === 'avatar') {
            try {
                $model->$k = SaveImageFromBase64(strval($v), "/upl/users/{$model->id}_".time());
            } catch (\Throwable $exception) {
            }
            continue;
        }
        $model->$k = $v;
    }
    if ($newPassword) {
        $model->pwd_hash = UserModel::makeHash($newPassword);
    }
}

$model->flush();
$userData = $model->getDataToApi();
unset($userData['pwd_hash']);

(new ApiResponse())->normal($userData);