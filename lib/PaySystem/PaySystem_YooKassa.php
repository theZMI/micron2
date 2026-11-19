<?php

namespace PaySystem;

use IShopOrderModel;
use PaymentModel;
use YooKassa\Client;

class PaySystem_YooKassa extends PaySystem_Base
{
    private Client $driver;
    private array $config;

    public function __construct()
    {
        $this->config = Config('pay_system');
        $this->driver = new Client();
        $this->driver->setAuth($this->config['connect']['shop_id'], $this->config['connect']['password']);
    }

    // Чек по 54-ФЗ. Нужен, если в ЮKassa подключена отправка чеков
    private function makeReceipt(IShopOrderModel $order): array
    {
        $customer = $order->getCustomer();
        $receipt  = ['customer' => [], 'items' => []];
        if ($customer['email']) {
            $receipt['customer']['email'] = $customer['email'];
        }
        if ($customer['phone'] && PhoneFilter($customer['phone'])) {
            $receipt['customer']['phone'] = ltrim(PhoneFilter($customer['phone']), '+');
        }
        if ($customer['name']) {
            $receipt['customer']['full_name'] = $this->plainText($customer['name'], 256);
        }
        if (!empty($this->config['receipt']['tax_system_code'])) {
            $receipt['tax_system_code'] = $this->config['receipt']['tax_system_code'];
        }

        foreach ($order->getPayItems() as $item) {
            $receiptItem = [
                'description'     => $this->plainText($item['name']),
                'quantity'        => (string)(float)$item['quantity'], // Бывает дробным: 2.5 м
                'amount'          => [
                    'value'    => $this->amountToBankFormat($item['price']), // Цена за единицу
                    'currency' => $order->getCurrency(),
                ],
                'vat_code'        => $this->config['receipt']['vat_code'],
                'payment_mode'    => 'full_prepayment',
                'payment_subject' => !empty($item['is_service']) ? 'service' : $this->config['receipt']['payment_subject'], // Доставка - услуга
            ];
            // Мера количества (ФФД 1.2): piece, meter, kilogram... - из Config('shop')['units'][...]['measure']
            if (!empty($this->config['receipt']['send_measure'])) {
                $receiptItem['measure'] = $item['measure'] ?? 'piece';
            }
            $receipt['items'][] = $receiptItem;
        }
        return $receipt;
    }

    protected function sendPaymentToBank(IShopOrderModel $order): array
    {
        $data = [
            'amount'       => [
                'value'    => $this->amountToBankFormat($order->getTotalAmount()),
                'currency' => $order->getCurrency(),
            ],
            'confirmation' => [
                'type'       => 'redirect',
                'return_url' => $order->getPayBackUrl(), // Куда вернуть покупателя после оплаты
            ],
            'capture'      => true, // Платёж в одну стадию
            'description'  => $this->plainText("Заказ №{$order->getId()}"),
            'metadata'     => ['shop_order_id' => $order->getId()],
        ];
        if (!empty($this->config['payment_method'])) {
            $data['payment_method_data'] = ['type' => $this->config['payment_method']]; // Иначе покупатель сам выберет способ (карта, СБП...)
        }
        if (!empty($this->config['receipt']['is_enabled'])) {
            $data['receipt'] = $this->makeReceipt($order);
        }

        // Ключ идемпотентности уникален для каждой попытки, иначе повторная оплата заказа вернёт старый (отменённый) платёж
        $idempotenceKey = "order-{$order->getId()}-" . bin2hex(random_bytes(8));
        $bankResponse   = $this->driver->createPayment($data, $idempotenceKey);

        return [
            'transfer_id' => $bankResponse->getId(),
            'pay_link'    => $bankResponse->getConfirmation()->getConfirmationUrl(),
        ];
    }

    public function getPaymentStatus(string $transfer_id): string
    {
        $info = $this->driver->getPaymentInfo($transfer_id);
        return match ($info->getStatus()) {
            'succeeded' => PaymentModel::STATUS_PAID,
            'canceled'  => PaymentModel::STATUS_FAILED,
            default     => PaymentModel::STATUS_WAITING, // pending, waiting_for_capture (при capture=true не бывает)
        };
    }

    // HTTP-уведомление ЮKassa: JSON {"type":"notification","event":"payment.succeeded","object":{"id":"..."}}
    public function getTransferIdFromNotification(array $get, string $body): ?string
    {
        $json = json_decode($body, true);
        $id   = $json['object']['id'] ?? null;
        return is_string($id) && preg_match('/^[\w-]{1,64}$/', $id) ? $id : null;
    }
}
