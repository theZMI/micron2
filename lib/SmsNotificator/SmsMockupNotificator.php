<?php

namespace SmsNotificator;

/**
 * Заглушка для локальной разработки: вместо отправки SMS пишет его в tmp/sms_mockup.log
 */
class SmsMockupNotificator implements \INotificator
{
    public function send($userTo, $message)
    {
        \FileLogger::create(BASEPATH . 'tmp/sms_mockup.log')->message(PhoneFilter($userTo->phone) . ": " . $message);
        return true;
    }
}
