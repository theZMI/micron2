<?php

namespace PaySystem;

use IPaymentModel;
use IShopOrderModel;

abstract class PaySystem_Base
{
    // Регистрирует платёж в банке. Надо вернуть: transfer_id (ID платежа в эквайринге), pay_link (куда отправить покупателя для оплаты)
    protected abstract function sendPaymentToBank(IShopOrderModel $order): array;

    // Статус платежа в банке, приведённый к PaymentModel::STATUS_* (waiting, paid, failed)
    public abstract function getPaymentStatus(string $transfer_id): string;

    /**
     * ID платежа из уведомления банка (webhook на pay_system/notify) или null, если запрос не похож на уведомление.
     * Самому уведомлению не верим: статус всё равно перепроверяется запросом к банку через getPaymentStatus()
     */
    public abstract function getTransferIdFromNotification(array $get, string $body): ?string;

    // Сумма в формате банка
    // Копейки -> рубли строкой с двумя знаками («1464.86») - без float
    protected function amountToBankFormat(int $amount): string
    {
        return ($amount < 0 ? '-' : '') . intdiv(abs($amount), 100) . '.' . str_pad((string)(abs($amount) % 100), 2, '0', STR_PAD_LEFT);
    }

    // В БД строки хранятся с экранированным HTML (см. Input::clean), а банку нужен чистый текст
    protected function plainText(string $text, int $maxLength = 128): string
    {
        $text = trim(strip_tags(html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
        return mb_substr($text, 0, $maxLength);
    }

    public function createPayment(IShopOrderModel $order, IPaymentModel $payment): string
    {
        if ($order->getTotalAmount() <= 0) {
            throw new \InvalidArgumentException("Order #{$order->getId()}: wrong amount");
        }

        $bankResponse = $this->sendPaymentToBank($order);

        $payment->user_id       = $order->getCustomer()['user_id'] ?? null;
        $payment->shop_order_id = $order->getId();
        $payment->amount        = $order->getTotalAmount();
        $payment->currency      = $order->getCurrency();
        $payment->transfer_id   = $bankResponse['transfer_id'];
        $payment->status        = \PaymentModel::STATUS_WAITING;
        $payment->pay_time      = time();
        $payment->pay_link      = $bankResponse['pay_link'];
        if (!$payment->flush()) {
            throw new \RuntimeException("Order #{$order->getId()}: can't save payment into local DB");
        }

        return (string)$bankResponse['pay_link'];
    }
}
