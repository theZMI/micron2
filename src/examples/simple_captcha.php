<?php

$captcha            = new SimpleCaptcha();
$genNewCaptchaCode  = function() use ($captcha) {
    $captcha->code();
    $captcha->image();
    $captcha->storeSession();
};
$checkCaptchaCode   = function(&$realCaptchaCode = '') use ($captcha) {
    $realCode = $captcha->getSession();
    $realCode = $realCaptchaCode = $realCode['code'] ?? '';
    $userCode = Post('captcha_code');

    $realCode = TranslateSimilarRuLettersToEn(mb_strtoupper($realCode));
    $userCode = TranslateSimilarRuLettersToEn(mb_strtoupper($userCode));
    return $realCode === $userCode;
};

// Обработка формы
$msg = '';
$isFormSubmit = !!Post('is_set');

if ($isFormSubmit) {
    $wasCaptchaCode = '';
    $isCorrectCaptchaCode = @$checkCaptchaCode($wasCaptchaCode);

    if (!$isCorrectCaptchaCode) {
        @$genNewCaptchaCode();
        $msg = MsgErr("Неправильный проверочный код.<br>Правильный был: {$wasCaptchaCode}, а пришёл: " . mb_strtoupper(Post('captcha_code')));
    }

    if ($isCorrectCaptchaCode) {
        $msg = MsgOk('Правильный проверочный код.<br>Вас зовут: ' . Post('username'));
    }
}

if (!$isFormSubmit) {
    @$genNewCaptchaCode();
}