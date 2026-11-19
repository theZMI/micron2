<?php

$order = new ShopOrderModel(intval(Get('id')) ?: null);
if (!$order->isExists()) {
    IncludeCom('404');
    ExitCom();
    return;
}

$msg = '';
switch (Post('a')) {
    case 'set_status':
        $oldStatus = $order->status;
        $order->setStatus((string)Post('status')); // При отмене остатки вернутся на склад; «Оплачен» вручную - например, наличными
        if (Post('notify') && $order->status !== $oldStatus) {
            $order->notifyCustomerAboutStatus();
        }
        UrlRedirect::go(GetCurUrl());
    case 'check_payments':
        foreach ($order->getPayments() as $payment) {
            try {
                $payment->checkStatusInBank();
            } catch (Throwable $e) {
                $msg .= MsgErr("Платёж #{$payment->id}: " . htmlspecialchars($e->getMessage()));
            }
        }
        if ($msg === '') {
            UrlRedirect::go(GetCurUrl());
        }
        break;
}
$payments = $order->getPayments();
