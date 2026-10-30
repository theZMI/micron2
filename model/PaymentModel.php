<?php

/**
 * Платёж, созданный через PaySystem.
 * Когда статус меняется на paid/failed, вызываются обработчики из Config(['pay_system', 'on_paid']) / Config(['pay_system', 'on_failed']),
 * в них приходит эта модель (например, чтобы отметить заказ оплаченным)
 */
class PaymentModel extends SiteModel implements IPaymentModel
{
    const STATUS_WAITING = 'waiting';
    const STATUS_PAID    = 'paid';
    const STATUS_FAILED  = 'failed';

    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'shop_order_id'    => 'int',
            'amount'           => 'float',
            'currency'         => 'string',
            'transfer_id'      => 'string',
            'status'           => 'string',
            'pay_link'         => 'string',
            'pay_time'         => 'int',
            'is_done'          => 'bool',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT DEFAULT NULL,
                `shop_order_id` INT DEFAULT NULL,
                `amount` DECIMAL(12, 2) DEFAULT NULL,
                `currency` VARCHAR(10) DEFAULT NULL,
                `transfer_id` VARCHAR(255) DEFAULT NULL,
                `status` VARCHAR(20) DEFAULT NULL,
                `pay_link` VARCHAR(2000) DEFAULT NULL,
                `pay_time` INT DEFAULT NULL,
                `is_done` TINYINT(1) NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                KEY `shop_order_id` (`shop_order_id`),
                KEY `status` (`status`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('payments', $id);
    }

    public static function getIdByShopOrderId($shop_order_id)
    {
        return GetDefaultDb()->selectCell(
            "SELECT `id` FROM `payments` WHERE `shop_order_id` = ?d ORDER BY `id` DESC LIMIT 1",
            $shop_order_id
        );
    }

    public function getWaitingList()
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `status` = ? AND `is_done` = 0", $this->table, self::STATUS_WAITING);
        return array_map(fn($id) => new static($id), $ids);
    }

    public function __set($key, $value)
    {
        $isNewFinalStatus = $key === 'status'
            && $this->isExists()
            && !$this->is_done
            && $value !== $this->status
            && in_array($value, [self::STATUS_PAID, self::STATUS_FAILED], true);

        $ret = parent::__set($key, $value);

        if ($isNewFinalStatus) {
            parent::__set('is_done', true);
            $this->flush();
            $handler = Config(['pay_system', $value === self::STATUS_PAID ? 'on_paid' : 'on_failed']);
            if (is_callable($handler)) {
                call_user_func($handler, $this);
            }
        }
        return $ret;
    }
}
