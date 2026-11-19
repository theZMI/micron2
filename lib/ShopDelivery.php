<?php

/**
 * Способы доставки и оплаты из Config('shop')['delivery'] и ['payment_methods']
 */
class ShopDelivery
{
    public static function methods(): array
    {
        return (array)Config('shop')['delivery'];
    }

    public static function defaultMethod(): string
    {
        return (string)array_key_first(self::methods());
    }

    public static function exists(string $method): bool
    {
        return isset(self::methods()[$method]);
    }

    public static function name(string $method): string
    {
        return (string)(self::methods()[$method]['name'] ?? $method);
    }

    public static function needAddress(string $method): bool
    {
        return !empty(self::methods()[$method]['need_address']);
    }

    // В настройках цены доставки - в рублях (их пишет человек), здесь - в копейках
    private static function configKopecks(string $method, string $key): int
    {
        return (int)ShopKopecks((string)(self::methods()[$method][$key] ?? 0));
    }

    // Стоимость доставки в копейках для суммы товаров в копейках (после скидки): бесплатно от free_from
    public static function price(string $method, int $itemsAmount): int
    {
        if (!self::exists($method)) {
            return 0;
        }
        $freeFrom = self::configKopecks($method, 'free_from');
        return $freeFrom > 0 && $itemsAmount >= $freeFrom ? 0 : self::configKopecks($method, 'price');
    }

    // Подпись цены в форме: «400 ₽, бесплатно от 5 000 ₽», «Бесплатно»
    public static function priceText(string $method, int $itemsAmount): string
    {
        $price    = self::price($method, $itemsAmount);
        $freeFrom = self::configKopecks($method, 'free_from');
        $text     = $price > 0 ? ShopPrice($price) : 'Бесплатно';
        if ($price > 0 && $freeFrom > 0) {
            $text .= ', бесплатно от ' . ShopPrice($freeFrom);
        }
        return $text;
    }

    public static function paymentMethods(): array
    {
        return (array)Config('shop')['payment_methods'];
    }

    public static function defaultPaymentMethod(): string
    {
        return (string)array_key_first(self::paymentMethods());
    }
}
