<?php

// Вход в приложение, шаг 2: POST login + code (из письма, см. api/v1.0/send_login_code) → данные пользователя и token.
// Дальше token передаётся в заголовке API-User-Auth-Token
$apiResponse = new ApiResponse();
$attempts    = new LoginAttemptModel();
$login       = trim((string)Post('login'));
$code        = trim((string)Post('code'));

if ($login === '' || $code === '') {
    $apiResponse->error('Укажите логин и код из письма');
}
if ($attempts->isBlocked('api', $login)) {
    $apiResponse->error('Слишком много неудачных попыток входа. Попробуйте через 15 минут', 429);
}

$user = UserModel::findActiveByLogin($login);
if (!$user || !(new UserAuthCodeModel())->isValidCode($user->id, $code)) {
    $attempts->registerFail('api', $login);
    $apiResponse->error('Неверный или просроченный код', 401);
}

$attempts->clear('api', $login);
if (!$user->is_email_verified) {
    // Владелец e-mail подтвердил его впервые: закрываем сессии, открытые до этого (например тем, кто вписал чужой e-mail в свой аккаунт)
    $user->revokeAllSessions();
}
$user->is_email_verified = true;
$user->flush();

$apiResponse->normal(
    array_merge($user->getPublicData(), ['token' => $user->issueApiToken()])
);
