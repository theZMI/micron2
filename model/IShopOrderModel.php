<?php

/**
 * Заказ, который оплачивается через PaySystem (готовая реализация - ShopOrderModel)
 */
interface IShopOrderModel
{
    public function getId(): int;

    public function getTotalAmount(): int; // В копейках (минимальных единицах валюты)

    public function getCurrency(): string; // RUB, USD...

    // Позиции для чека: [['name' => ..., 'vendor_code' => ..., 'price' => ..., 'quantity' => ..., 'amount' => ... (price и amount - в копейках),
    //   'unit' => 'шт.' (необязательно), 'is_service' => bool (доставка, необязательно)], ...]. Сумма amount = getTotalAmount()
    public function getPayItems(): array;

    // Покупатель: ['user_id' => ?int, 'name' => ..., 'email' => ..., 'phone' => ...]
    public function getCustomer(): array;

    // Куда банк вернёт покупателя после оплаты
    public function getPayBackUrl(): string;
}
