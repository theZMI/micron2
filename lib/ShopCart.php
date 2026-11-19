<?php

/**
 * Корзина магазина в сессии: [ключ строки => quantity] (ключ - id товара или «id:доп.услуги», см. lineKey) и промокод. Цены не храним - они берутся из товаров при показе и при оформлении.
 * Количество - в единицах товара (штуки, метры, килограммы), может быть дробным: кратно шагу товара (ShopProductModel::normalizeQuantity)
 */
class ShopCart
{
    private const SESSION_KEY = 'shop_cart';
    private const PROMO_KEY   = 'shop_cart_promo';

    // Пришлось уменьшить количество или убрать товары, потому что их не хватает на складе (для сообщения в корзине)
    public static bool $isAdjusted = false;

    private static function hasSession(): bool
    {
        // Нет сессии - нет корзины. Не стартуем сессию зря: счётчик корзины выводится в меню на каждой странице
        return !(session_id() === '' && empty($_COOKIE[session_name()])); // session_id() есть и после session_write_close() - сессию создали в этом запросе
    }

    private static function read(): array
    {
        if (!self::hasSession()) {
            return [];
        }
        $cart = SessionManager::get(self::SESSION_KEY);
        return is_array($cart) ? array_map('floatval', $cart) : [];
    }

    private static function write(array $cart): void
    {
        $cart ? SessionManager::set(self::SESSION_KEY, $cart) : SessionManager::delete(self::SESSION_KEY);
    }

    // ---- Строки корзины ----
    // Ключ строки: id товара или «id:доп.услуги» (22:1-3) - один товар с разными доп. услугами лежит отдельными строками

    public static function lineKey(int $product_id, array $addonIds = []): string
    {
        $addonIds = array_values(array_unique(array_filter(array_map('intval', $addonIds))));
        sort($addonIds);
        return $product_id . ($addonIds ? ':' . implode('-', $addonIds) : '');
    }

    // [id товара, [id доп. услуг]]
    public static function parseKey(string $key): array
    {
        [$product_id, $addons] = explode(':', $key, 2) + [1 => ''];
        return [(int)$product_id, $addons === '' ? [] : array_map('intval', explode('-', $addons))];
    }

    // Сколько товара уже в корзине (во всех строках, кроме $exceptKey)
    public static function quantity(int $product_id, ?string $exceptKey = null): float
    {
        $ret = 0.0;
        foreach (self::read() as $key => $quantity) {
            $ret += self::parseKey((string)$key)[0] === $product_id && (string)$key !== $exceptKey ? $quantity : 0;
        }
        return $ret;
    }

    /**
     * Положить товар (с выбранными доп. услугами - только из доступных товару). Тот же товар с теми же услугами - прибавляется к строке
     */
    public static function add(int $product_id, $quantity = null, array $addonIds = []): void
    {
        $product = new ShopProductModel($product_id ?: null);
        if (!$product->isExists()) {
            return;
        }
        $addonIds = array_intersect(array_map('intval', $addonIds), array_keys($product->getAddons()));
        $key      = self::lineKey($product_id, $addonIds);
        $quantity = $quantity === null || (float)str_replace(',', '.', (string)$quantity) <= 0 ? $product->minQuantity() : (float)str_replace(',', '.', (string)$quantity);
        self::set($key, (self::read()[$key] ?? 0) + $quantity, $product);
    }

    // Количество 0 - убрать строку из корзины. Количество приводится к шагу и минимуму товара
    public static function set(string $key, $quantity, ?ShopProductModel $product = null): void
    {
        $cart       = self::read();
        $product_id = self::parseKey($key)[0];
        $product    = $product ?? new ShopProductModel($product_id ?: null);
        // Один товар может лежать в нескольких строках (с разными доп. услугами) - остаток общий на все
        $quantity   = $product->isExists() ? $product->normalizeQuantity($quantity, $product->maxQuantity() - self::quantity($product_id, $key)) : 0.0;
        if ($product_id <= 0 || $quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key] = $quantity;
        }
        self::write($cart);
    }

    public static function remove(string $key): void
    {
        $cart = self::read();
        unset($cart[$key]);
        self::write($cart);
    }

    public static function clear(): void
    {
        self::write([]);
        self::setPromoCode('');
    }

    // Пустая ли корзина (без запросов к БД)
    public static function count(): int
    {
        return count(self::read());
    }

    /**
     * Строки корзины с актуальными товарами:
     * [['key', 'product' => ShopProductModel, 'quantity' => float, 'addons' => [id => ShopAddonModel], 'addon_prices' => [id => цена за раз],
     *   'addons_amount' => сумма доп. услуг, 'amount' => сумма строки с доп. услугами], ...]. Деньги - в копейках.
     * Снятые с продажи товары и недоступные больше доп. услуги убираются из корзины
     */
    public static function getItems(): array
    {
        $cart     = self::read();
        $products = (new ShopProductModel())->getActiveByIds(array_map(fn($key) => self::parseKey((string)$key)[0], array_keys($cart)));
        $changed  = false;

        // 1. Актуальные строки: снятые с продажи товары убираем, недоступные доп. услуги - тоже. Строки, у которых после этого
        //    совпал набор доп. услуг, сливаем в одну (иначе в корзине были бы две строки с одним ключом)
        $lines = [];
        foreach ($cart as $key => $quantity) {
            [$product_id, $addonIds] = self::parseKey((string)$key);
            if (!isset($products[$product_id])) {
                $changed = true;
                continue;
            }
            $addons = array_intersect_key($products[$product_id]->getAddons(), array_flip($addonIds));
            $newKey = self::lineKey($product_id, array_keys($addons));
            if ($newKey !== (string)$key) {
                $changed = true; // Доп. услугу убрали из групп товара
            }
            $lines[$newKey] = ['product_id' => $product_id, 'addons' => $addons, 'quantity' => ($lines[$newKey]['quantity'] ?? 0) + (float)$quantity];
        }

        // 2. Количество: по правилам товара и не больше остатка - остаток общий на все строки одного товара
        $items   = [];
        $newCart = [];
        $used    = []; // Сколько каждого товара уже взяли предыдущие строки
        foreach ($lines as $key => $line) {
            $product    = $products[$line['product_id']];
            $normalized = $product->normalizeQuantity($line['quantity'], $product->maxQuantity() - ($used[$line['product_id']] ?? 0));
            $used[$line['product_id']] = ($used[$line['product_id']] ?? 0) + $normalized;
            if (abs($normalized - $line['quantity']) > 0.0001) {
                $changed          = true;
                self::$isAdjusted = true;
            }
            if ($normalized <= 0) {
                continue;
            }
            $addonPrices    = array_map(fn(ShopAddonModel $a) => $a->priceFor($product->price), $line['addons']);
            $addonsCents    = array_sum(array_map(fn($price) => self::lineCents($price, $product->addonQuantity($normalized)), $addonPrices));
            $newCart[$key]  = $normalized;
            $items[] = [
                'key'           => (string)$key,
                'product'       => $product,
                'quantity'      => $normalized,
                'addons'        => $line['addons'],
                'addon_prices'  => $addonPrices,
                'addons_amount' => $addonsCents,
                'amount'        => self::lineCents($product->price, $normalized) + $addonsCents,
            ];
        }

        if ($changed) {
            self::write($newCart);
        }
        return $items;
    }

    /**
     * Позиции для заказа и чека: товар и отдельными позициями его доп. услуги.
     * [['item' => индекс строки корзины, 'product' => ..., 'addon' => ShopAddonModel|null, 'price' => за единицу в копейках, 'quantity' => ...], ...]
     */
    public static function orderLines(array $items): array
    {
        $lines = [];
        foreach (array_values($items) as $i => $item) {
            $lines[] = ['item' => $i, 'product' => $item['product'], 'addon' => null, 'price' => (int)$item['product']->price, 'quantity' => (float)$item['quantity']];
            foreach ($item['addons'] as $id => $addon) {
                $lines[] = ['item' => $i, 'product' => $item['product'], 'addon' => $addon, 'price' => (int)$item['addon_prices'][$id], 'quantity' => $item['product']->addonQuantity($item['quantity'])];
            }
        }
        return $lines;
    }

    // Сумма строки в копейках: цена × количество (количество бывает дробным - округляем до копейки)
    public static function lineCents(int $price, float $quantity): int
    {
        return (int)round($price * $quantity);
    }

    /**
     * Для индикатора в меню: «товаров» (штучные - по количеству, весовые/метражные - по 1 за позицию) и сумма
     */
    public static function summary(): array
    {
        if (self::count() === 0) {
            return ['count' => 0, 'total' => 0];
        }
        $items = self::getItems();
        return ['count' => self::itemsCount($items), 'total' => self::total($items)];
    }

    public static function itemsCount(array $items): int
    {
        return (int)array_sum(array_map(fn($i) => $i['product']->isPieceUnit() ? (int)round($i['quantity']) : 1, $items));
    }

    // Сумма строк корзины в копейках
    public static function total(array $items): int
    {
        return (int)array_sum(array_column($items, 'amount'));
    }

    // ---- Промокод ----

    public static function promoCode(): string
    {
        return self::hasSession() ? (string)SessionManager::get(self::PROMO_KEY) : '';
    }

    public static function setPromoCode(string $code): void
    {
        $code === '' ? (self::hasSession() && SessionManager::delete(self::PROMO_KEY)) : SessionManager::set(self::PROMO_KEY, $code);
    }

    /**
     * Итоги корзины с промокодом и доставкой (то же считает заказ при оформлении):
     * ['items' => сумма товаров, 'discount' => скидка, 'delivery' => доставка, 'total' => к оплате (всё в копейках),
     *  'promo' => ShopPromoCodeModel|null, 'promo_error' => почему промокод не применился]
     */
    public static function totals(array $items, ?string $deliveryMethod = null): array
    {
        $itemsAmount = self::total($items);
        $promo       = null;
        $promoError  = '';
        $discount    = 0;
        if (($code = self::promoCode()) !== '') {
            $promo      = ShopPromoCodeModel::findByCode($code);
            $promoError = $promo->checkError($itemsAmount);
            if ($promoError === '') {
                $discount = ShopPromoCodeModel::discountedItems(self::orderLines($items), $promo)['discount'];
            } else {
                $promo = null;
            }
        }
        $delivery = $deliveryMethod !== null ? ShopDelivery::price($deliveryMethod, $itemsAmount - $discount) : 0;
        return [
            'items'       => $itemsAmount,
            'discount'    => $discount,
            'delivery'    => $delivery,
            'total'       => $itemsAmount - $discount + $delivery,
            'promo'       => $promo,
            'promo_error' => $promoError,
        ];
    }
}
