<?php

$email         = 'support@itkd.ru';
$emailName     = 'Support ITKD';
$emailPassword = '3eJ655GAnv';

Config('sendmail', [
    'replayTo'     => [$email, $emailName],
    'sender'       => [$email, $emailName],
    'smtp'         => [
        'use'    => true,
        'auth'   => true,
        'secure' => 'ssl',
        'host'   => 'mail.hostland.ru',
        'port'   => 465,
        'email'  => $email,
        'pwd'    => $emailPassword
    ],
    'send_to_file' => EnvConfig::DEBUG_MODE
]);