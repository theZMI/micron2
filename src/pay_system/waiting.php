<?php

if (!class_exists('ShopOrderModel')) {
    throw new Exception('Создайте в проекте модель заказа ShopOrderModel (implements IShopOrderModel)');
}

$shop_order_id   = intval(Get('id'));
$shopOrderModel  = new ShopOrderModel($shop_order_id);
$paymentModel    = new PaymentModel(PaymentModel::getIdByShopOrderId($shop_order_id));
$isOrderExists   = $shopOrderModel->isExists();
$isPaymentExists = $paymentModel->isExists();
$isAlreadyDone   = $isPaymentExists ? $paymentModel->is_done : false;
$status          = $isAlreadyDone ? $paymentModel->status : PaySystem::getInstance()->getPaymentStatus($paymentModel->transfer_id);

if (!$isOrderExists || !$isPaymentExists) {
    throw new Exception('Не найден подходящий платёж');
}

$paymentModel->status = $status;

if ($status === 'paid') {
    UrlRedirect::go(SiteRoot("pay_system/succeeded&id={$shop_order_id}"));
}
if ($status === 'failed') {
    UrlRedirect::go(SiteRoot("pay_system/failed&id={$shop_order_id}"));
}