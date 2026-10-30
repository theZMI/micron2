<?php

// Проверка ответа Google reCAPTCHA v2 (поле g-recaptcha-response из формы)
function CheckRecaptchaCode($userToken): bool
{
    if (empty($userToken) || RECAPTCHA_SECRET_KEY === '') {
        return false;
    }

    require_once BASEPATH . 'lib/LIB_http.php';
    $response = http_post_form(
        'https://www.google.com/recaptcha/api/siteverify',
        '',
        [
            'secret'   => RECAPTCHA_SECRET_KEY,
            'response' => $userToken,
            'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
        ]
    );
    $result = json_decode($response['FILE'] ?? '', true);

    return !empty($result['success']);
}
