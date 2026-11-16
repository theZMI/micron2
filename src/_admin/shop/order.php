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
        $status = (string)Post('status');
        if (array_key_exists($status, ShopOrderModel::getStatuses())) {
            $order->status = $status;
            if ($status === ShopOrderModel::STATUS_PAID && !$order->paid_time) {
                $order->paid_time = time(); // Например, оплатили наличными
            }
            $order->flush();
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
