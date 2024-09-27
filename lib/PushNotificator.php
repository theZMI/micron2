<?php

use JeanJar\OneSignal\PushNotification;

class PushNotificator implements INotificator
{
    public function send($userTo, $message)
    {
        $auth      = Config('push_notificator');
        $driver    = new PushNotification($auth['api_id'], $auth['rest_api_key']);
        $device_id = $userTo->device_id;
        $isError   = true;

        if (!$device_id) {
            return false;
        }
        try {
            $response = $driver->setBody($message)
                ->setPlayersId($device_id)
                ->prepare()
                ->send();
            $response = json_decode(stripslashes($response), true);
            $isError  = empty($response) || isset($response['errors']);
        } catch (\Throwable $e) {
        }

        return !$isError;
    }
}