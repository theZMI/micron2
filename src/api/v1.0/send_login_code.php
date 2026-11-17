<?php

// Вход в приложение, шаг 1: POST login → на e-mail пользователя уходит одноразовый код.
// Шаг 2 - api/v1.0/login (login + code). Паролей нет
$apiResponse = new ApiResponse();
$attempts    = new LoginAttemptModel();
$login       = trim((string)Post('login'));

if ($login === '') {
    $apiResponse->error('Укажите логин (e-mail)');
}
if ($attempts->isBlocked('api', $login)) {
    $apiResponse->error('Слишком много неудачных попыток входа. Попробуйте через 15 минут', 429);
}

$user = UserModel::findActiveByLogin($login);
if (!$user) {
    $attempts->registerFail('api', $login); // Перебор логинов тоже ограничиваем
    $apiResponse->error('Не найден пользователь с таким логином', 404);
}

$codes  = new UserAuthCodeModel();
$status = $codes->sendByEmail($user, UserAuthCodeModel::PURPOSE_LOGIN);
if ($status === UserAuthCodeModel::SEND_FAIL) {
    $apiResponse->error('Не удалось отправить код, попробуйте чуть позже', 503);
}

$apiResponse->normal([
    'is_sent'      => $status === UserAuthCodeModel::SEND_OK, // false - код уже отправлен недавно, новый пока не отправляли
    'sent_to'      => $user->maskedEmail(),                   // Куда ушёл код, вида i***@example.com
    'code_length'  => UserAuthCodeModel::CODE_LENGTH,
    'resend_after' => $codes->resendAfter($user->id),         // Через сколько секунд можно запросить код ещё раз
    'ttl'          => (int)Config(['user_model', 'auth_code_ttl']),
]);
