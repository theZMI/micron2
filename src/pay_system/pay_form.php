<?php

// Оплата заказа: pay_system/pay_form&id=ID&key=KEY (ссылку даёт ShopOrderModel::url('pay_system/pay_form'))
$order = ShopOrderModel::findForCurrentVisitor();
if (!$order) {
    IncludeCom('404');
    ExitCom();
    return;
}
if (!$order->canPay()) {
    UrlRedirect::go($order->url());
}

$msg = '';
if (Post('is_set')) {
    try {
        // Если прошлый платёж ещё ждёт оплаты - отправляем на него же, а не создаём новый
        $lastPayment = PaymentModel::getLastByShopOrderId($order->id);
        $status      = $lastPayment->checkStatusInBank();
        if ($status === PaymentModel::STATUS_PAID) {
            UrlRedirect::go($order->url('pay_system/succeeded'));
        }
        if ($status === PaymentModel::STATUS_WAITING && $lastPayment->pay_link !== '') {
            UrlRedirect::go($lastPayment->pay_link);
        }

        $bankUrl = PaySystem::getInstance()->createPayment($order, new PaymentModel());
        UrlRedirect::go($bankUrl); // Отправляем покупателя на оплату
    } catch (Throwable $e) {
        ToLog("Shop order #{$order->id}: can't create payment: " . $e->getMessage());
        $msg = MsgErr('Не удалось связаться с платёжной системой. Попробуйте ещё раз через несколько минут.');
    }
}
