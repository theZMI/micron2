<?php

/**
 * «Вы недавно смотрели»: последние открытые товары в сессии
 */
class ShopRecentlyViewed
{
    private const SESSION_KEY = 'shop_recently_viewed';
    private const LIMIT       = 12;

    private static function ids(): array
    {
        if (session_id() === '' && empty($_COOKIE[session_name()])) {
            return [];
        }
        $ids = SessionManager::get(self::SESSION_KEY);
        return is_array($ids) ? array_map('intval', $ids) : [];
    }

    public static function add(int $product_id): void
    {
        $ids = array_values(array_diff(self::ids(), [$product_id]));
        array_unshift($ids, $product_id);
        SessionManager::set(self::SESSION_KEY, array_slice($ids, 0, self::LIMIT));
    }

    // Просмотренные товары (новые первыми), кроме $exceptId
    public static function getProducts(int $exceptId = 0, int $limit = 4): array
    {
        $ids      = array_values(array_diff(self::ids(), [$exceptId]));
        $products = (new ShopProductModel())->getActiveByIds($ids);
        return array_slice(array_values(array_filter(array_map(fn($id) => $products[$id] ?? null, $ids))), 0, $limit);
    }
}
