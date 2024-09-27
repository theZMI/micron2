<?php

namespace SmsNotificator;

class SmscNotificator implements \INotificator
{
    public function send($userTo, $message)
    {
        $phone = PhoneFilter($userTo->phone);
        $isOK  = false;
        try {
            $auth = Config('smsc_notificator');
            $url  = 'https://smsc.ru/sys/send.php'
                . "?sender={$auth['sender']}"
                . '&login=' . urlencode($auth['login'])
                . "&psw=" . urlencode($auth['password'])
                . '&phones=' . urlencode($phone)
                . '&mes=' . urlencode($message);
            $ret  = file_get_contents($url);
            $isOK = stripos($ret, 'ERROR = ') === false;
        } catch (\Throwable $e) {
        }
        return $isOK;
    }
}
