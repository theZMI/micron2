<?php

// Ввод одноразового кода из письма - вход и регистрация без пароля. Служебный компонент:
//  - IncludeCom('user/_email_code', ['user' => $user, 'purpose' => ..., 'notice' => ...]) - отправить код и показать форму
//  - без параметров (форма отправлена на user/login) - проверить код или отправить его повторно
// Кто ждёт код, хранится в сессии, поэтому в форме нет ни логина, ни e-mail
$sessionKey = 'auth_code_user_id';

if (IS_USER_AUTH) {
    UrlRedirect::go(AuthRedirectUrl());
}

$codes  = new UserAuthCodeModel();
$msg    = '';
$notice = $notice ?? '';

// Отправка кода: сразу после ввода логина / регистрации или по кнопке «Отправить код ещё раз»
if (!isset($user) && Post('is_resend_email_code')) {
    $user = new UserModel((int)SessionManager::get($sessionKey) ?: null);
}
if (isset($user)) {
    if (!$user->isExists() || $user->status === UserModel::STATUS_DELETE) {
        UrlRedirect::go(SiteRoot('user/login')); // Сессия истекла или аккаунт удалён - начинаем вход заново
    }
    SessionManager::set($sessionKey, (int)$user->id);
    $status = $codes->sendByEmail($user, $purpose ?? UserAuthCodeModel::PURPOSE_LOGIN);
    $msg    = match ($status) {
        UserAuthCodeModel::SEND_WAIT => MsgWarning('Код уже отправлен, новый можно запросить через ' . $codes->resendAfter($user->id) . ' сек.'),
        UserAuthCodeModel::SEND_FAIL => MsgErr('Не удалось отправить код, попробуйте чуть позже'),
        default                      => $notice,
    };
}

// Проверка кода
$user_id = (int)SessionManager::get($sessionKey);
if (!$user_id) {
    UrlRedirect::go(SiteRoot('user/login'));
}
$user = new UserModel($user_id);
if ($user->status === UserModel::STATUS_DELETE) {
    SessionManager::delete($sessionKey);
    UrlRedirect::go(SiteRoot('user/login'));
}

if (Post('is_check_email_code')) {
    if ($codes->isValidCode($user_id, Post('user_code'))) {
        SessionManager::delete($sessionKey);
        if (!$user->is_email_verified) {
            // Владелец e-mail подтвердил его впервые: закрываем сессии, открытые до этого (например тем, кто вписал чужой e-mail в свой аккаунт)
            $user->revokeAllSessions();
        }
        $user->is_email_verified = true;
        $user->flush();
        $user->startSession();
        UrlRedirect::go(AuthRedirectUrl()); // Обратно в магазин, если вход начался оттуда (см. RememberAuthBackUrl)
    }
    $msg = MsgErr('Неверный или просроченный код');
}

$sentTo = $user->maskedEmail();
