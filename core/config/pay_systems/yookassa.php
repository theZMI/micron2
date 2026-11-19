<?php

Config('pay_system_yookassa', [
    'connect'        => [
        'shop_id'  => Env('PAY_SYSTEM_YOOKASSA_SHOP_ID'),
        'password' => Env('PAY_SYSTEM_YOOKASSA_PASSWORD'),
    ],
    'payment_method' => null, // null - покупатель сам выберет способ на странице ЮKassa (карта, СБП, SberPay...), 'bank_card' - только карта
    'receipt'        => [ // Чек по 54-ФЗ (если в ЮKassa подключены чеки). Коды: https://yookassa.ru/developers/payment-acceptance/receipts/54fz/parameters-values
        'is_enabled'      => true,
        'tax_system_code' => null, // Система налогообложения (1 - ОСН, 2 - УСН доходы...). null - взять из настроек магазина в ЮKassa
        'vat_code'        => 1, // Ставка НДС (1 - без НДС)
        'payment_subject' => 'commodity', // Предмет расчёта: commodity - товар, service - услуга
        'send_measure'    => true, // Передавать меру количества (measure: piece, meter, kilogram...) - нужна кассам на ФФД 1.2
    ],
    'driver'         => 'PaySystem\PaySystem_YooKassa',
]);
