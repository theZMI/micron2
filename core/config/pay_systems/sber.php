<?php

Config('pay_system_sber', [
    'connect'      => [
        'userName' => 'P3918005008-api', // 'kkosa-api',
        'password' => 'ubuntu!zmipwd!@#2', // 'kkosa',
        'apiUri'   => Voronkovich\SberbankAcquiring\Client::API_URI, // Voronkovich\SberbankAcquiring\Client::API_URI_TEST,
    ],
    'payment_name' => 'Покупка на сайте', // Название платежа для Сбер-а которым он подписывает письмо для юзера (указываем что-то логичное, например: Покупка билета на посещение национального парка)
    'driver'       => 'PaySystem_Sber'
]);