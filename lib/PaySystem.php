<?php

abstract class PaySystem
{
    // Надо вернуть массив из двух полей: transfer_id (ID-платежка в эквайринге), pay_link (ссылка на которую нужно перебросить юзера для оплаты)
    protected abstract function sendPaymentToBank($customerModel, IShopOrderModel $shopOrderModel): array;

    protected function savePaymentToLocalDB($customerModel, IShopOrderModel $shopOrderModel, IPaymentModel $paymentModel, $transfer_id, $pay_link): int|bool
    {
        $paymentModel->user_id       = $customerModel->id;
        $paymentModel->shop_order_id = $shopOrderModel->id;
        $paymentModel->amount        = $shopOrderModel->total_amount;
        $paymentModel->currency      = $shopOrderModel->currency;
        $paymentModel->transfer_id   = $transfer_id;
        $paymentModel->status        = 'waiting';
        $paymentModel->pay_time      = time();
        $paymentModel->pay_link      = $pay_link;
        return $paymentModel->flush();
    }

    protected function _amountToFormat(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    protected function _getBackUrl(IShopOrderModel $shopOrderModel): string
    {
        return SiteRoot("pay_system/waiting&id={$shopOrderModel->id}");
    }

    public function createPayment($customerModel, IShopOrderModel $shopOrderModel, IPaymentModel $paymentModel): string
    {
        $bankResponse = $this->sendPaymentToBank(
            $customerModel,
            $shopOrderModel
        );
        $isSaved      = $this->savePaymentToLocalDB(
            $customerModel,
            $shopOrderModel,
            $paymentModel,
            $bankResponse['transfer_id'],
            $bankResponse['pay_link']
        );
        if (!$isSaved) {
            throw new Exception("Error to save payment into local DB");
        }

        return (string)$paymentModel->pay_link;
    }

    public abstract function getPaymentStatus($transfer_id): string;
}