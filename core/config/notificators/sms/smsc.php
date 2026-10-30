<?php

Config('smsc_notificator', [
    'sender'   => Env('SMSC_SENDER'),
    'login'    => Env('SMSC_LOGIN'),
    'password' => Env('SMSC_PASSWORD'),
]);
