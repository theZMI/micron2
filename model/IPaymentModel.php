<?php

/**
 * @property int $user_id
 * @property int $shop_order_id
 * @property string $transfer_id
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property int $pay_time
 * @property string $pay_link
 */
interface IPaymentModel
{
    // Получить ID-платежа который привязан к ID-заказа в магазине
    public static function getIdByShopOrderId(int $shop_order_id): int;
}