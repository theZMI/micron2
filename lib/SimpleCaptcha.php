<?php

class SimpleCaptcha extends \LordDashMe\SimpleCaptcha\Captcha
{
    protected function allowedCodeCharacters()
    {
        return 'ABCDEFGHJKLMNPRSTUVWXYZ23456789';
    }
}