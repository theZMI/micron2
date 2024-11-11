<?php

namespace SmsNotificator;

class SmsRuNotificator implements \INotificator
{
    public function send($userTo, $message)
    {
        try {
            $auth    = Config('smsru_notificator');
            $phone   = PhoneFilter($userTo->phone);
            $url     = "http://api.prostor-sms.ru/messages/v2/send.json";
            $content = json_encode([
                'login'    => $auth['login'],
                'password' => $auth['password'],
                'messages' => [
                    'phone' => str_replace('+', '', $phone),
                    'text'  => $message
                ]
            ]);

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ["Content-type: application/json"]);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

            $json_response = curl_exec($curl);
            $status        = +curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $response      = json_decode($json_response, true);
            curl_close($curl);

            $isOk = in_array($status, [200, 201]) && isset($response['status']) && $response['status'] == 'ok';
        } catch (\Exception $e) {
            $isOk = false;
        }

        return $isOk;
    }
}