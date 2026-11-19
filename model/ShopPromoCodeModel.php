<?php

/**
 * Промокод: скидка в процентах или фиксированной суммой на товары (не на доставку).
 * Ограничения: срок действия, минимальная сумма заказа, сколько раз можно использовать.
 * Скидка раскладывается по ценам позиций (discountedItems) - так её правильно видит чек (54-ФЗ)
 */
class ShopPromoCodeModel extends SiteModel
{
    const TYPE_PERCENT = 'percent';
    const TYPE_FIXED   = 'fixed';

    public function scheme()
    {
        return [
            'id'               => 'int',
            'code'             => 'string', // Хранится в верхнем регистре, вводить можно как угодно
            'type'             => 'string',
            'value'            => 'int', // Скидка: в копейках (fixed) или в сотых долях процента (percent: 1000 = 10%)
            'min_amount'       => 'int', // Минимальная сумма товаров в копейках (0 - любая)
            'max_uses'         => 'int', // Сколько раз можно использовать (0 - без ограничений)
            'used_count'       => 'int',
            'start_time'       => 'int', // 0 - сразу
            'end_time'         => 'int', // 0 - бессрочно
            'is_active'        => 'bool',
            'comment'          => 'string',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `code` VARCHAR(50) NOT NULL DEFAULT '',
                `type` VARCHAR(10) NOT NULL DEFAULT 'percent',
                `value` BIGINT NOT NULL DEFAULT 0,
                `min_amount` BIGINT NOT NULL DEFAULT 0,
                `max_uses` INT NOT NULL DEFAULT 0,
                `used_count` INT NOT NULL DEFAULT 0,
                `start_time` INT NOT NULL DEFAULT 0,
                `end_time` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `comment` VARCHAR(255) NOT NULL DEFAULT '',
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                UNIQUE KEY `code` (`code`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_promo_codes', $id);
    }

    public static function types(): array
    {
        return [self::TYPE_PERCENT => 'Процент', self::TYPE_FIXED => 'Сумма'];
    }

    public static function normalizeCode(string $code): string
    {
        return mb_strtoupper(trim(preg_replace('/\s+/u', '', html_entity_decode($code, ENT_QUOTES))));
    }

    public static function findByCode(string $code): self
    {
        new self(); // Таблица могла ещё не создаться
        $code = self::normalizeCode($code);
        $id   = $code === '' ? null : GetDefaultDb()->selectCell("SELECT `id` FROM `shop_promo_codes` WHERE `code` = ? LIMIT 1", htmlspecialchars($code));
        return new self($id ?: null);
    }

    // «−10%» или «−500 ₽»
    public function valueText(): string
    {
        return '−' . ($this->type === self::TYPE_FIXED ? ShopPrice($this->value) : self::percentText($this->value) . '%');
    }

    // Сотые доли процента -> «7,5»
    public static function percentText(int $hundredths): string
    {
        return rtrim(rtrim(number_format($hundredths / 100, 2, ',', ''), '0'), ',');
    }

    // Почему промокод нельзя применить к сумме товаров в копейках ('' - можно)
    public function checkError(int $itemsAmount): string
    {
        $now = time();
        return match (true) {
            !$this->isExists() || !$this->is_active                    => 'Такого промокода нет',
            $this->start_time > 0 && $now < $this->start_time          => 'Промокод начнёт действовать ' . date('d.m.Y', $this->start_time),
            $this->end_time > 0 && $now > $this->end_time              => 'Срок действия промокода закончился',
            $this->max_uses > 0 && $this->used_count >= $this->max_uses => 'Промокод уже использован',
            $this->min_amount > 0 && $itemsAmount < $this->min_amount   => 'Промокод действует при заказе от ' . ShopPrice($this->min_amount),
            default                                                     => '',
        };
    }

    /**
     * Раскладывает скидку по позициям ($items из ShopCart::orderLines(): товары и доп. услуги): новая цена за единицу у каждой позиции,
     * пропорционально её сумме. Возвращает ['items' => [i => ['price' => новая цена, 'amount' => новая сумма]], 'discount' => итоговая скидка] - всё в копейках.
     * Скидка может отличаться от номинала на копейки, если все позиции дробные (2.5 м) - из-за округления цен за единицу
     */
    public static function discountedItems(array $items, self $promo): array
    {
        // $items - позиции с ценой за единицу в копейках и количеством (ShopCart::orderLines: товары и доп. услуги)
        $totalCents = array_sum(array_map(fn($i) => ShopCart::lineCents((int)$i['price'], (float)$i['quantity']), $items));
        if ($totalCents <= 0) {
            return ['items' => [], 'discount' => 0];
        }
        $discountCents = $promo->type === self::TYPE_FIXED
            ? min($totalCents, max(0, $promo->value))
            : intdiv($totalCents * min(10000, max(0, $promo->value)) + 5000, 10000); // value - сотые доли процента, округляем до копейки
        $ratio = 1 - $discountCents / $totalCents;

        $prices = [];
        foreach ($items as $i => $item) {
            $prices[$i] = (int)round((int)$item['price'] * $ratio);
        }
        // Копейки, не попавшие в скидку из-за округления цен, отдаём позиции, где они раскладываются ровно
        // (целое количество, на которое делится остаток) - тогда скидка ровно номинальная
        $lineSum  = fn() => array_sum(array_map(fn($i) => ShopCart::lineCents($prices[$i], (float)$items[$i]['quantity']), array_keys($items)));
        $residual = ($totalCents - $discountCents) - $lineSum();
        foreach ($items as $i => $item) {
            $quantity = (float)$item['quantity'];
            if ($residual !== 0 && fmod($quantity, 1.0) == 0.0 && $residual % (int)$quantity === 0 && $prices[$i] + $residual / $quantity > 0) {
                $prices[$i] += intdiv($residual, (int)$quantity);
                $residual    = 0;
            }
        }

        $ret      = [];
        $newTotal = 0;
        foreach ($items as $i => $item) {
            $lineCents = ShopCart::lineCents($prices[$i], (float)$item['quantity']);
            $ret[$i]   = ['price' => $prices[$i], 'amount' => $lineCents];
            $newTotal += $lineCents;
        }
        return ['items' => $ret, 'discount' => $totalCents - $newTotal];
    }

    /**
     * Отмечает использование при оформлении заказа (внутри транзакции). Если лимит уже исчерпан другим заказом - исключение
     */
    public static function useCode(int $id): void
    {
        $affected = GetDefaultDb()->query(
            "UPDATE `shop_promo_codes` SET `used_count` = `used_count` + 1 WHERE `id` = ?d AND (`max_uses` = 0 OR `used_count` < `max_uses`)",
            $id
        );
        if (!$affected) {
            throw new ShopCheckoutException('Промокод уже использован');
        }
    }

    public function getAll(): array
    {
        return array_map(fn($id) => new static($id), $this->db->selectCol("SELECT `id` FROM ?# ORDER BY `id` DESC", $this->table));
    }
}
