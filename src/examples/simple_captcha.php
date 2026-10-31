<?php

$captcha = GetCaptchaInstance();

// Обработка формы
$msg = '';
$isFormSubmit = !!Post('is_set');

if ($isFormSubmit) {
    $wasCaptchaCode = '';
    $isCorrectCaptchaCode = CheckCaptchaCode(Post('captcha_code'), $wasCaptchaCode);

    @GenNewCaptchaCode(); // Старый код после проверки уже недействителен
    if (!$isCorrectCaptchaCode) {
        $msg = MsgErr("Неправильный проверочный код.<br>Правильный был: {$wasCaptchaCode}, а пришёл: " . mb_strtoupper(Post('captcha_code')));
    }

    if ($isCorrectCaptchaCode) {
        $msg = MsgOk('Правильный проверочный код.<br>Вас зовут: ' . Post('username'));
    }
}

if (!$isFormSubmit) {
    @GenNewCaptchaCode();
}