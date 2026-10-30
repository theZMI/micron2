<?php

ini_set('memory_limit', '512M');

$apiResponse = new ApiResponse();

// Пользователь может читать и менять только свои данные
if (isset($id) && (int)$id !== (int)$g_user->id) {
    $apiResponse->error('Access denied', 403);
}

$model         = $g_user;
$newPassword   = (string)Post('new_password');
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
];

if (Post('is_set')) {
    foreach ($canEditFields as $k) {
        if (!isset($_POST[$k])) {
            continue;
        }
        if ($k === 'avatar') {
            try {
                $model->avatar = SaveImageFromBase64(strval($_POST[$k]), "/upl/users/" . +$model->id . '_' . time());
            } catch (\Throwable $exception) {
            }
            continue;
        }
        if ($k === 'email' && Post($k) !== $model->email && $model->isEmailBusy(Post($k))) {
            $apiResponse->error('Пользователь с таким e-mail уже зарегистрирован');
        }
        $model->$k = Post($k);
    }
    if ($newPassword !== '') {
        $model->pwd_hash = UserModel::hashPassword($newPassword);
        $model->flush();
        $model->revokeAllSessions(); // Старые токены больше не действуют, выдаём новый
        $apiResponse->normal(array_merge($model->getPublicData(), ['token' => $model->issueApiToken()]));
    }
    $model->flush();
}

$apiResponse->normal($model->getPublicData());
