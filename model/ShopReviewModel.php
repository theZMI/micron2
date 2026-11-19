<?php

/**
 * Отзыв о товаре: оценка 1-5 и текст. Оставляют только вошедшие пользователи, по одному на товар.
 * «Покупатель» - если у пользователя есть оплаченный/выполненный заказ с этим товаром.
 * При модерации (Config('shop')['reviews']['moderation']) отзыв виден после одобрения в админке.
 * Средняя оценка и количество хранятся у товара (rating, reviews_count) - для карточек и сортировки
 */
class ShopReviewModel extends SiteModel
{
    const STATUS_NEW       = 'new';
    const STATUS_PUBLISHED = 'published';
    const STATUS_REJECTED  = 'rejected';

    public function scheme()
    {
        return [
            'id'               => 'int',
            'product_id'       => 'int',
            'user_id'          => 'int',
            'author_name'      => 'string',
            'rating'           => 'int',
            'text'             => 'string',
            'is_buyer'         => 'bool',
            'status'           => 'string',
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
                `user_id` INT NOT NULL,
                `author_name` VARCHAR(255) NOT NULL DEFAULT '',
                `rating` TINYINT NOT NULL DEFAULT 5,
                `text` TEXT DEFAULT NULL,
                `is_buyer` TINYINT(1) NOT NULL DEFAULT 0,
                `status` VARCHAR(20) NOT NULL DEFAULT 'new',
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                UNIQUE KEY `product_user` (`product_id`, `user_id`),
                KEY `status` (`status`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_reviews', $id);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_NEW       => 'На модерации',
            self::STATUS_PUBLISHED => 'Опубликован',
            self::STATUS_REJECTED  => 'Отклонён',
        ];
    }

    public static function isEnabled(): bool
    {
        return !empty(Config('shop')['reviews']['is_enabled']);
    }

    // Опубликованные отзывы товара, новые первыми
    public function getPublished(int $product_id, int $limit = 0): array
    {
        $ids = $this->db->selectCol(
            "SELECT `id` FROM ?# WHERE `product_id` = ?d AND `status` = ? ORDER BY `create_time` DESC, `id` DESC {LIMIT ?d}",
            $this->table,
            $product_id,
            self::STATUS_PUBLISHED,
            $limit > 0 ? $limit : DBSIMPLE_SKIP
        );
        return array_map(fn($id) => new static($id), $ids);
    }

    // Сколько отзывов с каждой оценкой: [5 => 10, 4 => 2, ...]
    public function ratingDistribution(int $product_id): array
    {
        $counts = array_map('intval', $this->db->selectCol(
            "SELECT `rating` AS ARRAY_KEY, COUNT(*) FROM ?# WHERE `product_id` = ?d AND `status` = ? GROUP BY `rating`",
            $this->table,
            $product_id,
            self::STATUS_PUBLISHED
        ));
        $ret = [];
        foreach (range(5, 1) as $rating) {
            $ret[$rating] = $counts[$rating] ?? 0;
        }
        return $ret;
    }

    public static function findByUser(int $product_id, int $user_id): self
    {
        new self();
        $id = GetDefaultDb()->selectCell("SELECT `id` FROM `shop_reviews` WHERE `product_id` = ?d AND `user_id` = ?d", $product_id, $user_id);
        return new self($id ?: null);
    }

    // Покупал ли пользователь товар (оплаченный или выполненный заказ)
    public static function isBuyer(int $product_id, int $user_id): bool
    {
        return (bool)GetDefaultDb()->selectCell(
            "SELECT 1 FROM `shop_order_items` i JOIN `shop_orders` o ON o.`id` = i.`shop_order_id`
              WHERE i.`product_id` = ?d AND o.`user_id` = ?d AND o.`status` IN (?a) LIMIT 1",
            $product_id,
            $user_id,
            [ShopOrderModel::STATUS_PAID, ShopOrderModel::STATUS_DONE]
        );
    }

    // Пересчитывает среднюю оценку и количество отзывов у товара
    public static function recalcProduct(int $product_id): void
    {
        $db  = GetDefaultDb();
        $row = $db->selectRow(
            "SELECT COUNT(*) AS `cnt`, COALESCE(AVG(`rating`), 0) AS `avg` FROM `shop_reviews` WHERE `product_id` = ?d AND `status` = ?",
            $product_id,
            self::STATUS_PUBLISHED
        );
        $db->query(
            "UPDATE `shop_products` SET `rating` = ?, `reviews_count` = ?d WHERE `id` = ?d",
            (string)round((float)$row['avg'], 2),
            (int)$row['cnt'],
            $product_id
        );
    }

    public function setStatus(string $status): void
    {
        if (!isset(self::getStatuses()[$status])) {
            return;
        }
        $this->status = $status;
        $this->flush();
        self::recalcProduct($this->product_id);
    }

    public function delete()
    {
        $product_id = $this->product_id;
        $ret        = parent::delete();
        if ($ret) {
            self::recalcProduct($product_id);
        }
        return $ret;
    }

    public function getAll(?string $status = null): array
    {
        $ids = $this->db->selectCol(
            "SELECT `id` FROM ?# WHERE 1 { AND `status` = ? } ORDER BY `create_time` DESC, `id` DESC",
            $this->table,
            $status ?? DBSIMPLE_SKIP
        );
        return array_map(fn($id) => new static($id), $ids);
    }

    public static function countNew(): int
    {
        new self();
        return (int)GetDefaultDb()->selectCell("SELECT COUNT(*) FROM `shop_reviews` WHERE `status` = ?", self::STATUS_NEW);
    }
}
