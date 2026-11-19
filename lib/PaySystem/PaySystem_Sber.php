<?php

namespace PaySystem;

use IShopOrderModel;
use PaymentModel;
use Voronkovich\SberbankAcquiring\Client;
use Voronkovich\SberbankAcquiring\Currency;
use Voronkovich\SberbankAcquiring\OrderStatus;

class PaySystem_Sber extends PaySystem_Base
{
    private Client $driver;
    private array $config;

    private const CURRENCIES = [
        'RUB' => Currency::RUB,
        'UAH' => Currency::UAH,
        'USD' => Currency::USD,
        'EUR' => Currency::EUR,
    ];

    public function __construct()
    {
        $this->config = Config('pay_system');
        $this->driver = new Client($this->config['connect']);
    }

    // Сбер принимает суммы в копейках
    protected function amountToBankFormat(int $amount): string
    {
        return (string)$amount; // Суммы у нас уже в копейках
    }

    private function makeOrderBundle(IShopOrderModel $order): array
    {
        $items = [];
        foreach (array_values($order->getPayItems()) as $pos => $item) {
            $items[] = [
                'positionId' => (string)($pos + 1),
                'name'       => $this->plainText($item['name'], 100),
                'quantity'   => [
                    'value'   => (float)$item['quantity'], // Бывает дробным: 2.5 м
                    'measure' => $item['unit'] ?? 'шт.',
                ],
                'itemAmount' => (int)$this->amountToBankFormat($item['amount']),
                'itemCode'   => $this->plainText($item['vendor_code'] ?: "item-{$pos}", 100),
                'tax'        => ['taxType' => $this->config['tax_type']],
                'itemPrice'  => (int)$this->amountToBankFormat($item['price']),
            ];
        }

        $bundle   = ['cartItems' => ['items' => $items]];
        $customer = $order->getCustomer();
        if ($customer['email']) {
            $bundle['customerDetails']['email'] = $customer['email'];
        }
        if ($customer['phone'] && PhoneFilter($customer['phone'])) {
            $bundle['customerDetails']['phone'] = PhoneFilter($customer['phone']);
        }
        return $bundle;
    }

    protected function sendPaymentToBank(IShopOrderModel $order): array
    {
        $currency = self::CURRENCIES[$order->getCurrency()] ?? null;
        if ($currency === null) {
            throw new \InvalidArgumentException("Sber: unsupported currency {$order->getCurrency()}");
        }

        // Номер заказа в Сбере должен быть уникальным для каждой попытки оплаты
        $bankResponse = $this->driver->registerOrder(
            $order->getId() . '-' . time(),
            (int)$this->amountToBankFormat($order->getTotalAmount()),
            $order->getPayBackUrl(),
            [
                'currency'    => $currency,
                'description' => $this->plainText($this->config['payment_name'] . " (заказ №{$order->getId()})", 99),
                'failUrl'     => $order->getPayBackUrl(), // Страница ожидания сама узнает в банке, что оплата не прошла
                'orderBundle' => $this->makeOrderBundle($order),
            ]
        );

        return [
            'transfer_id' => $bankResponse['orderId'],
            'pay_link'    => $bankResponse['formUrl'],
        ];
    }

    public function getPaymentStatus(string $transfer_id): string
    {
        $result = $this->driver->getOrderStatus($transfer_id);
        if (OrderStatus::isDeposited($result['orderStatus'])) {
            return PaymentModel::STATUS_PAID;
        }
        if (OrderStatus::isDeclined($result['orderStatus']) || OrderStatus::isReversed($result['orderStatus']) || OrderStatus::isRefunded($result['orderStatus'])) {
            return PaymentModel::STATUS_FAILED;
        }
        return PaymentModel::STATUS_WAITING;
    }

    // Callback-уведомление Сбера: GET ?mdOrder=...&orderNumber=...&operation=deposited&status=1
    public function getTransferIdFromNotification(array $get, string $body): ?string
    {
        $id = $get['mdOrder'] ?? null;
        return is_string($id) && preg_match('/^[\w-]{1,64}$/', $id) ? $id : null;
    }
}
