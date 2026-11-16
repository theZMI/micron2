<?php

/**
 * Заказ, который оплачивается через PaySystem (готовая реализация - ShopOrderModel)
 */
interface IShopOrderModel
{
    public function getId(): int;

    public function getTotalAmount(): float;

    public function getCurrency(): string; // RUB, USD...

    // Позиции для чека: [['name' => ..., 'vendor_code' => ..., 'price' => ..., 'quantity' => ..., 'amount' => ...], ...]
    public function getPayItems(): array;

    // Покупатель: ['user_id' => ?int, 'name' => ..., 'email' => ..., 'phone' => ...]
    public function getCustomer(): array;

    // Куда банк вернёт покупателя после оплаты
    public function getPayBackUrl(): string;
}
