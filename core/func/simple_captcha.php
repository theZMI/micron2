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
    $captcha       = GetCaptchaInstance();
    $realCode      = $captcha->getSession();
    $realCode      = $realCode['code'] ?? '';
    $outputWasCode = $realCode;

    // Код одноразовый: после проверки (верной или нет) он больше не подходит, иначе один раз разгаданный код можно слать бесконечно
    SessionManager::delete('LDM_SIMPLE_CAPTCHA');

    $realCode = TranslateSimilarRuLettersToEn(mb_strtoupper((string)$realCode));
    $userCode = TranslateSimilarRuLettersToEn(mb_strtoupper(trim((string)$userCode)));
    return $realCode !== '' && $realCode === $userCode; // Без кода в сессии (капчу не показывали) проверка не проходит
}