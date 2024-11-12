<?php

namespace PaySystem;

use IShopOrderModel;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;
use Voronkovich\SberbankAcquiring\OrderStatus;

class PaySystem_Sber extends \PaySystem\PaySystem_Base
{
    private ?Client $driver;

    public function __construct()
    {
        $this->driver = new Client(Config(['pay_system', 'connect']));
    }

    protected function _amountToFormat(float $amount): string
    {
        return 100 * parent::_amountToFormat($amount);
    }

    protected function sendPaymentToBank($customerModel, IShopOrderModel $shopOrderModel): array
    {
        $makeOrderData                 = function () use (&$customerModel, &$shopOrderModel) {
            $pos        = 1;
            $orderItems = [];
            foreach ($shopOrderModel->items as $item) {
                $orderItems[] = [
                    'positionId' => "$pos",
                    'name'       => strip_tags($item->name),
                    'quantity'   => [
                        'value'   => 1,
                        'measure' => 'шт.',
                    ],
                    'itemAmount' => $this->_amountToFormat((float)$item->amount),
                    'itemCode'   => $item->vendor_code,
                    'tax'        => [
                        "taxType" => 0,
                        "taxSum"  => 0
                    ],
                    'itemPrice'  => $this->_amountToFormat((float)$item->amount)
                ];
                $pos++;
            }

            return [
                'cartItems' => [
                    'items' => $orderItems,
                ],
            ];
        };
        $getBankCurrencyBySiteCurrency = function ($siteCurrencyName) {
            $arr = [
                "RUB" => Currency::RUB,
                "UAH" => Currency::UAH,
                "USD" => Currency::USD,
                "EUR" => Currency::EUR,
            ];
            return $arr[$siteCurrencyName];
        };

        $bankResponse = $this->driver->registerOrder(
            $shopOrderModel->id,
            $this->_amountToFormat($shopOrderModel->total_amount),
            $this->_getBackUrl($shopOrderModel),
            [
                'currency'    => $getBankCurrencyBySiteCurrency($shopOrderModel->currency),
                'description' => Config(['pay_system', 'payment_name']),
                'failUrl'     => SiteRoot('pay_system/fail'),
                'orderBundle' => $makeOrderData
            ]
        );

        return [
            'transfer_id' => $bankResponse['orderId'],
            'pay_link'    => $bankResponse['formUrl']
        ];
    }

    public function getPaymentStatus($transfer_id): string
    {
        $result = $this->driver->getOrderStatus($transfer_id);
        if (OrderStatus::isDeposited($result['orderStatus'])) {
            return 'paid';
        } elseif (OrderStatus::isDeclined($result['orderStatus'])) {
            return 'failed';
        }
        return 'waiting';
    }
}