<?php

if (Post('is_set')) {
    // $shopOrderModel - модель текущего заказа пользователя
    if (!isset($shopOrderModel)) {
        throw new Exception('Не найден заказ');
    }
    if ($shopOrderModel->total_amount <= 0) {
        throw new Exception('Ошибка в сумме заказа');
    }
    if (!IS_USER_AUTH) {
        throw new Exception('Невозможно создать заказ для анонимного юзера');
    }

    $bankUrl = PaySystem::getInstance()->createPayment(
        $g_user,
        $shopOrderModel,
        new PaymentModel()
    );
    UrlRedirect::go($bankUrl); // Отправляем человека на оплату заказа
}