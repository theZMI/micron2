<?php

/**
 * Сравнение товаров: список id в сессии (до MAX товаров). Страница сравнения - shop/compare:
 * таблица характеристик, можно показать только различающиеся
 */
class ShopCompare
{
    private const SESSION_KEY = 'shop_compare';
    const MAX = 8;

    public static function ids(): array
    {
        // Не стартуем сессию зря: счётчик выводится в меню на каждой странице
        if (session_id() === '' && empty($_COOKIE[session_name()])) {
            return [];
        }
        $ids = SessionManager::get(self::SESSION_KEY);
        return is_array($ids) ? array_map('intval', $ids) : [];
    }

    public static function has(int $product_id): bool
    {
        return in_array($product_id, self::ids(), true);
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    // Добавляет или убирает товар. Возвращает true, если теперь товар в сравнении (самый старый вытесняется после MAX)
    public static function toggle(int $product_id): bool
    {
        $isAdd = !self::has($product_id);
        $ids   = $isAdd ? array_merge(self::ids(), [$product_id]) : array_diff(self::ids(), [$product_id]);
        $ids   = array_slice(array_values(array_unique($ids)), -self::MAX);
        $ids ? SessionManager::set(self::SESSION_KEY, $ids) : SessionManager::delete(self::SESSION_KEY);
        return $isAdd;
    }

    public static function clear(): void
    {
        SessionManager::delete(self::SESSION_KEY);
    }

    public static function getProducts(): array
    {
        $ids      = self::ids();
        $products = (new ShopProductModel())->getActiveByIds($ids);
        return array_values(array_filter(array_map(fn($id) => $products[$id] ?? null, $ids)));
    }

    /**
     * Строки таблицы: ['Состав' => ['values' => [значение по каждому товару], 'is_same' => bool], ...]
     */
    public static function rows(array $products): array
    {
        $names = [];
        foreach ($products as $product) {
            $names += array_flip(array_keys($product->getParams()));
        }
        $rows = [];
        foreach (array_keys($names) as $name) {
            $values      = array_map(fn($p) => (string)($p->getParams()[$name] ?? ''), $products);
            $normalized  = array_unique(array_map(fn($v) => mb_strtolower(trim($v)), $values));
            $rows[$name] = ['values' => $values, 'is_same' => count($normalized) === 1];
        }
        return $rows;
    }
}
