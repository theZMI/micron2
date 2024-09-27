<?php

use YooKassa\Client;


class PaySystem_YooKassa extends PaySystem
{
    private ?Client $driver;

    public function __construct()
    {
        $this->driver = new Client();
        $this->driver->setAuth(Config(['pay_system', 'connect', 'shop_id']), Config(['pay_system', 'connect', 'password']));
    }

    protected function sendPaymentToBank($customerModel, IShopOrderModel $shopOrderModel): array
    {
        $makeOrderData = function () use (&$customerModel, &$shopOrderModel) {
            $orderItems = [];
            foreach ($shopOrderModel->items as $item) {
                $orderItems[] = [
                    "description"     => strip_tags($item->name),
                    "quantity"        => "1.00", // Количество
                    "amount"          => [
                        "value"    => $this->_amountToFormat((float)$item->amount),
                        "currency" => $shopOrderModel->currency
                    ],
                    "tax_system_code" => "2", // Налогообложение
                    "vat_code"        => "1",
                    "payment_mode"    => "full_prepayment", // Полный платеж
                    "payment_subject" => "commodity"
                ];
            }

            return [
                "amount"              => [
                    "value"    => $this->_amountToFormat((float)$shopOrderModel->total_amount), // Сумма платежа
                    "currency" => $shopOrderModel->currency // Валюта платежа
                ],
                'payment_method_data' => [
                    'type' => 'bank_card',
                ],
                "confirmation"        => [
                    "type"       => "redirect",
                    "return_url" => $this->_getBackUrl($shopOrderModel) // Куда отправлять пользователя после оплаты
                ],
                "capture"             => true, // Платеж в один этап
                "receipt"             => [
                    "customer" => [
                        "email" => $customerModel->email,
                        "phone" => PhoneFilter($customerModel->phone)
                    ],
                    "items"    => $orderItems,
                    "email"    => $customerModel->email,
                    "phone"    => PhoneFilter($customerModel->phone)
                ]
            ];
        };

        $bankResponse = $this->driver->createPayment(
            $makeOrderData(),
            $shopOrderModel->id
        );
        return [
            'transfer_id' => $bankResponse->getid(),
            'pay_link'    => $bankResponse->getConfirmation()->getConfirmationUrl()
        ];
    }

    public function getPaymentStatus($transfer_id): string
    {
        $info = $this->driver->getPaymentInfo($transfer_id);
        return match ($info->getstatus()) {
            'waiting_for_capture', 'succeeded' => 'paid',
            'canceled'                         => 'failed',
            default                            => 'waiting'
        };
    }
}