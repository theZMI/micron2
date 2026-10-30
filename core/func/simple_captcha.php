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

    $realCode = TranslateSimilarRuLettersToEn(mb_strtoupper($realCode));
    $userCode = TranslateSimilarRuLettersToEn(mb_strtoupper($userCode));
    return $realCode === $userCode;
}