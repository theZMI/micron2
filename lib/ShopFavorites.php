<?php

/**
 * Избранные товары: у гостя - в сессии, у пользователя - в БД (shop_favorites).
 * Когда гость входит, его избранное из сессии переносится в аккаунт
 */
class ShopFavorites
{
    private const SESSION_KEY = 'shop_favorites';

    private static function userId(): int
    {
        global $g_user;
        return defined('IS_USER_AUTH') && IS_USER_AUTH ? (int)$g_user->id : 0;
    }

    private static function sessionIds(): array
    {
        // Не стартуем сессию зря: счётчик избранного выводится в меню на каждой странице
        if (session_id() === '' && empty($_COOKIE[session_name()])) { // session_id() есть и после session_write_close() - сессию создали в этом запросе
            return [];
        }
        $ids = SessionManager::get(self::SESSION_KEY);
        return is_array($ids) ? array_map('intval', $ids) : [];
    }

    public static function ids(): array
    {
        static $merged = false;
        $user_id = self::userId();
        if (!$user_id) {
            return self::sessionIds();
        }

        $db = GetDefaultDb();
        if (!$merged && ($guestIds = self::sessionIds())) {
            foreach ($guestIds as $id) {
                $db->query("INSERT IGNORE INTO `shop_favorites` SET `user_id` = ?d, `product_id` = ?d, `create_time` = ?d", $user_id, $id, time());
            }
            SessionManager::delete(self::SESSION_KEY);
        }
        $merged = true;
        return array_map('intval', $db->selectCol("SELECT `product_id` FROM `shop_favorites` WHERE `user_id` = ?d ORDER BY `create_time` DESC", $user_id));
    }

    public static function has(int $product_id): bool
    {
        return in_array($product_id, self::ids(), true);
    }

    public static function count(): int
    {
        return count(self::ids());
    }

    // Добавляет или убирает товар. Возвращает true, если теперь товар в избранном
    public static function toggle(int $product_id): bool
    {
        $isAdd   = !self::has($product_id);
        $user_id = self::userId();
        if ($user_id) {
            $isAdd
                ? GetDefaultDb()->query("INSERT IGNORE INTO `shop_favorites` SET `user_id` = ?d, `product_id` = ?d, `create_time` = ?d", $user_id, $product_id, time())
                : GetDefaultDb()->query("DELETE FROM `shop_favorites` WHERE `user_id` = ?d AND `product_id` = ?d", $user_id, $product_id);
        } else {
            $ids = $isAdd ? array_merge([$product_id], self::sessionIds()) : array_diff(self::sessionIds(), [$product_id]);
            SessionManager::set(self::SESSION_KEY, array_values(array_unique($ids)));
        }
        return $isAdd;
    }

    // Активные товары из избранного (в порядке добавления, новые первыми)
    public static function getProducts(): array
    {
        $ids      = self::ids();
        $products = (new ShopProductModel())->getActiveByIds($ids);
        return array_values(array_filter(array_map(fn($id) => $products[$id] ?? null, $ids)));
    }
}
