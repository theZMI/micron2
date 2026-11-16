<?php

// Сюда банк возвращает покупателя (и при успехе, и при отказе): узнаём статус и отправляем на нужную страницу
$order = ShopOrderModel::findForCurrentVisitor();
if (!$order) {
    IncludeCom('404');
    ExitCom();
    return;
}

$payment = PaymentModel::getLastByShopOrderId($order->id);
if (!$payment->isExists()) {
    UrlRedirect::go($order->url('pay_system/pay_form'));
}

$msg = '';
try {
    $status = $payment->checkStatusInBank();
} catch (Throwable $e) {
    ToLog("Payment #{$payment->id}: can't get status: " . $e->getMessage());
    $status = PaymentModel::STATUS_WAITING;
    $msg    = MsgWarning('Не удалось узнать статус платежа в банке. Обновите страницу через минуту.');
}

if ($status === PaymentModel::STATUS_PAID) {
    UrlRedirect::go($order->url('pay_system/succeeded'));
}
if ($status === PaymentModel::STATUS_FAILED) {
    UrlRedirect::go($order->url('pay_system/failed'));
}
