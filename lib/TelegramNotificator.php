<?php

class TelegramNotificator implements INotificator
{
    public function send($userTo, $message)
    {
        $url = "https://api.telegram.org/bot" . Config('telegram_notificator')['token']
            . "/sendMessage?chat_id={$userTo->telegram_chat_id}"
            . "&text=" . urlencode($message);
        $ch  = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true
        ]);
        $res = curl_exec($ch);
        curl_close($ch);

        return $res;
    }
}