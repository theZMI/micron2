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
    'telegram_login', // telegram_chat_id сюда не входит: его привязывает бот, когда человек сам напишет ему /start
    'first_name',
    'surname',
    'patronymic',
    'gender',
    'birthday_date',
    'notification_by_phone',
    'notification_by_email',
    'notification_by_telegram',
];

// Контакты, по которым приходят коды и уведомления: после смены их нужно подтвердить заново
$verifyFlags = [
    'email'          => 'is_email_verified',
    'phone'          => 'is_phone_verified',
    'telegram_login' => 'is_telegram_verified',
];

if (Post('is_set')) {
    // Сменить пароль можно, только зная текущий (иначе украденный токен даёт навсегда захватить аккаунт)
    if ($newPassword !== '' && !$model->verifyPassword((string)Post('old_password'))) {
        $apiResponse->error('Неверный текущий пароль', 403);
    }

    foreach ($canEditFields as $k) {
        if (!isset($_POST[$k]) || !is_scalar($_POST[$k])) {
            continue;
        }
        if ($k === 'avatar') {
            try {
                $model->avatar = SaveImageFromBase64(strval($_POST[$k]), "/upl/users/" . +$model->id . '_' . time());
            } catch (\Throwable $exception) {
            }
            continue;
        }
        $value     = (string)Post($k);
        $isChanged = isset($verifyFlags[$k]) && ($k === 'phone' ? PhoneFilter($value) : $value) !== (string)$model->$k;
        if ($isChanged && $value !== '') {
            $isBusy = match ($k) {
                'email'          => $model->isEmailBusy($value),
                'phone'          => $model->isPhoneBusy(PhoneFilter($value)),
                'telegram_login' => (bool)$model->findOne(['telegram_login' => $value]),
            };
            if ($isBusy) {
                $apiResponse->error('Этот ' . ['email' => 'e-mail', 'phone' => 'телефон', 'telegram_login' => 'телеграм'][$k] . ' уже указан у другого пользователя');
            }
        }
        $model->$k = $value;
        if ($isChanged) {
            $model->{$verifyFlags[$k]} = 0;
            if ($k === 'telegram_login') { // Уведомления больше не должны уходить в чат прежнего телеграма
                $model->telegram_chat_id         = '';
                $model->notification_by_telegram = 0;
            }
        }
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
