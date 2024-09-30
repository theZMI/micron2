<?php

Config('sendmail', [
    'replayTo'     => [Env('EMAIL_ADDRESS'), Env('EMAIL_NAME')],
    'sender'       => [Env('EMAIL_ADDRESS'), Env('EMAIL_NAME')],
    'smtp'         => [
        'use'    => true,
        'auth'   => true,
        'secure' => 'ssl',
        'host'   => Env('EMAIL_HOST'),
        'port'   => Env('EMAIL_PORT'),
        'email'  => Env('EMAIL_ADDRESS'),
        'pwd'    => Env('EMAIL_PASSWORD')
    ],
    'send_to_file' => Env('DEBUG_MODE')
]);