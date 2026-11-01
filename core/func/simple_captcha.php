<?php

function GetCaptchaInstance(): ?SimpleCaptcha
{
    static $captcha = null;
    if (is_null($captcha)) {
        $captcha = new SimpleCaptcha();
    }
    return $captcha;
}

function GenNewCaptchaCode(): void
{
    $captcha = GetCaptchaInstance();
    $captcha->code();
    $captcha->image();
    $captcha->storeSession();
}

function CheckCaptchaCode($userCode, &$outputWasCode = ''): bool
{
    // Код, уже принятый в этом запросе, принимаем повторно: компоненты бывают вложены друг в друга
    // (например user/login подключает user/password_forgot) и проверяют капчу каждый сам
    static $passedCode = null;
    $userCode = TranslateSimilarRuLettersToEn(mb_strtoupper(trim((string)$userCode)));
    if (!is_null($passedCode) && $passedCode === $userCode) {
        $outputWasCode = $passedCode;
        return true;
    }

    $captcha       = GetCaptchaInstance();
    $realCode      = $captcha->getSession();
    $realCode      = $realCode['code'] ?? '';
    $outputWasCode = $realCode;

    // Код одноразовый: после проверки (верной или нет) он больше не подходит, иначе один раз разгаданный код можно слать бесконечно
    SessionManager::delete('LDM_SIMPLE_CAPTCHA');

    $realCode = TranslateSimilarRuLettersToEn(mb_strtoupper((string)$realCode));
    $isValid  = $realCode !== '' && $realCode === $userCode; // Без кода в сессии (капчу не показывали) проверка не проходит
    if ($isValid) {
        $passedCode = $userCode;
    }
    return $isValid;
}
