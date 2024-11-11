<?php

Config('pay_system_yookassa', [
    'connect' => [
        'shop_id'  => Env('PAY_SYSTEM_YOOKASSA_SHOP_ID'),
        'password' => Env('PAY_SYSTEM_YOOKASSA_PASSWORD'),
    ],
    'driver'  => 'PaySystem\PaySystem_YooKassa'
]);