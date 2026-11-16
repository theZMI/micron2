<?php

/**
 * Корзина магазина в сессии: [product_id => quantity]. Цены не храним - они берутся из товаров при показе и при оформлении
 */
class ShopCart
{
    private const SESSION_KEY = 'shop_cart';

    private static function read(): array
    {
        // Нет сессии - нет корзины. Не стартуем сессию зря: счётчик корзины выводится в меню на каждой странице
        if (session_id() === '' && empty($_COOKIE[session_name()])) { // session_id() есть и после session_write_close() - сессию создали в этом запросе
            return [];
        }
        $cart = SessionManager::get(self::SESSION_KEY);
        return is_array($cart) ? $cart : [];
    }

    private static function write(array $cart): void
    {
        $cart ? SessionManager::set(self::SESSION_KEY, $cart) : SessionManager::delete(self::SESSION_KEY);
    }

    private static function normalizeQuantity($quantity): int
    {
        return max(0, min((int)$quantity, (int)Config('shop')['max_quantity']));
    }

    public static function add(int $product_id, int $quantity = 1): void
    {
        $cart = self::read();
        self::set($product_id, ($cart[$product_id] ?? 0) + $quantity);
    }

    // Количество 0 - убрать товар из корзины
    public static function set(int $product_id, $quantity): void
    {
        $cart     = self::read();
        $quantity = self::normalizeQuantity($quantity);
        if ($product_id <= 0 || $quantity === 0) {
            unset($cart[$product_id]);
        } else {
            $cart[$product_id] = $quantity;
        }
        self::write($cart);
    }

    public static function remove(int $product_id): void
    {
        self::set($product_id, 0);
    }

    public static function clear(): void
    {
        self::write([]);
    }

    public static function count(): int
    {
        return array_sum(self::read());
    }

    /**
     * Позиции корзины с актуальными товарами: [['product' => ShopProductModel, 'quantity' => int, 'amount' => float], ...].
     * Снятые с продажи и удалённые товары убираются из корзины
     */
    public static function getItems(): array
    {
        $cart     = self::read();
        $products = (new ShopProductModel())->getActiveByIds(array_keys($cart));

        $items = [];
        foreach ($cart as $product_id => $quantity) {
            if (!isset($products[$product_id])) {
                continue;
            }
            $product = $products[$product_id];
            $items[] = [
                'product'  => $product,
                'quantity' => (int)$quantity,
                'amount'   => ShopOrderModel::toCents($product->price) * $quantity / 100,
            ];
        }

        if (count($items) !== count($cart)) {
            self::write(array_column(array_map(fn($i) => [$i['product']->id, $i['quantity']], $items), 1, 0));
        }
        return $items;
    }

    // Для индикатора в меню: количество товаров и сумма
    public static function summary(): array
    {
        if (self::count() === 0) {
            return ['count' => 0, 'total' => 0.0];
        }
        $items = self::getItems();
        return ['count' => array_sum(array_column($items, 'quantity')), 'total' => self::total($items)];
    }

    public static function total(array $items): float
    {
        return array_sum(array_map(fn($i) => ShopOrderModel::toCents($i['amount']), $items)) / 100;
    }
}
