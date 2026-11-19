<?php

/**
 * «Сообщить о поступлении»: e-mail покупателя для товара, которого нет в наличии.
 * Когда товар снова есть (сохранение в админке, импорт, отмена заказа, крон _cron/shop_stock_notify) - письмо и подписка закрывается
 */
class ShopStockSubscriptionModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'product_id'       => 'int',
            'email'            => 'string',
            'notified_time'    => 'int', // 0 - ещё ждёт
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `product_id` INT NOT NULL,
                `email` VARCHAR(255) NOT NULL DEFAULT '',
                `notified_time` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `waiting` (`notified_time`, `product_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_stock_subscriptions', $id);
    }

    // Подписать e-mail (повторная подписка на тот же товар не дублируется)
    public static function subscribe(int $product_id, string $email): void
    {
        new self();
        $exists = GetDefaultDb()->selectCell(
            "SELECT 1 FROM `shop_stock_subscriptions` WHERE `product_id` = ?d AND `email` = ? AND `notified_time` = 0",
            $product_id,
            $email
        );
        if (!$exists) {
            $sub             = new self();
            $sub->product_id = $product_id;
            $sub->email      = $email;
            $sub->flush();
        }
    }

    public static function countWaiting(int $product_id): int
    {
        new self();
        return (int)GetDefaultDb()->selectCell("SELECT COUNT(*) FROM `shop_stock_subscriptions` WHERE `product_id` = ?d AND `notified_time` = 0", $product_id);
    }

    /**
     * Отправляет письма по товарам, которые снова в наличии. Возвращает, сколько писем отправлено
     */
    public static function notifyAvailable(int|array|null $productIds = null): int
    {
        // $productIds - товар или список товаров, которые могли появиться (null - проверить все подписки)
        $productIds = $productIds === null ? null : array_values(array_unique(array_filter(array_map('intval', (array)$productIds))));
        if ($productIds === []) {
            return 0;
        }
        new self();
        $db   = GetDefaultDb();
        $rows = $db->select(
            "SELECT `id`, `product_id`, `email` FROM `shop_stock_subscriptions` WHERE `notified_time` = 0 { AND `product_id` IN (?a) }",
            $productIds ?? DBSIMPLE_SKIP
        );
        $sent     = 0;
        $products = [];
        foreach ($rows as $row) {
            $pid     = (int)$row['product_id'];
            $product = $products[$pid] ??= new ShopProductModel($pid);
            if (!$product->isExists() || !$product->is_active || !$product->isAvailable()) {
                continue;
            }
            // Отмечаем до отправки: если письмо упадёт - не будем слать его снова и снова
            $db->query("UPDATE `shop_stock_subscriptions` SET `notified_time` = ?d WHERE `id` = ?d", time(), $row['id']);
            try {
                SendMail(
                    html_entity_decode($row['email']),
                    "«{$product->name}» снова в наличии",
                    "<p>Здравствуйте!</p><p>Товар, который вы ждали, снова в наличии: <a href=\"{$product->url()}\">{$product->name}</a> - " . ShopPrice($product->price) . $product->priceUnitSuffix() . ".</p>"
                );
                $sent++;
            } catch (Throwable $e) {
                ToLog("Shop stock subscription #{$row['id']}: can't send email: " . $e->getMessage());
            }
        }
        return $sent;
    }
}
