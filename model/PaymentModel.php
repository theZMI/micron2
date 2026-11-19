<?php

/**
 * Платёж, созданный через PaySystem.
 * Статус меняется только через applyStatus(): когда он становится paid/failed, вызываются обработчики из
 * Config(['pay_system', 'on_paid']) / Config(['pay_system', 'on_failed']) с этой моделью (см. core/init/pay_system.php)
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
            'amount'           => 'int', // В копейках
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
                `amount` BIGINT DEFAULT NULL,
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
                KEY `transfer_id` (`transfer_id`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('payments', $id);
    }

    public static function getStatuses(): array
    {
        return [
            self::STATUS_WAITING => 'Ожидает',
            self::STATUS_PAID    => 'Оплачен',
            self::STATUS_FAILED  => 'Не прошёл',
        ];
    }

    // Последний платёж по заказу (или пустая модель)
    public static function getLastByShopOrderId(int $shop_order_id): self
    {
        $id = GetDefaultDb()->selectCell(
            "SELECT `id` FROM `payments` WHERE `shop_order_id` = ?d ORDER BY `id` DESC LIMIT 1",
            $shop_order_id
        );
        return new self($id ?: null);
    }

    public static function findByTransferId(string $transfer_id): self
    {
        $id = $transfer_id === '' ? null : GetDefaultDb()->selectCell(
            "SELECT `id` FROM `payments` WHERE `transfer_id` = ? LIMIT 1",
            $transfer_id
        );
        return new self($id ?: null);
    }

    public function getListByShopOrderId(int $shop_order_id): array
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `shop_order_id` = ?d ORDER BY `id` DESC", $this->table, $shop_order_id);
        return array_map(fn($id) => new static($id), $ids);
    }

    public function getWaitingList(): array
    {
        $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `status` = ? AND `is_done` = 0", $this->table, self::STATUS_WAITING);
        return array_map(fn($id) => new static($id), $ids);
    }

    /**
     * Ставит статус из банка. Итоговый статус (paid/failed) ставится одним UPDATE ... WHERE is_done = 0, поэтому
     * если крон, webhook и возврат покупателя придут одновременно, обработчик on_paid/on_failed выполнится ровно один раз.
     * Возвращает true, если именно этот вызов перевёл платёж в итоговый статус
     */
    public function applyStatus(string $status): bool
    {
        if (!$this->isExists() || $this->is_done || $status === $this->status) {
            return false;
        }
        if (!in_array($status, [self::STATUS_PAID, self::STATUS_FAILED], true)) {
            $this->status = $status;
            $this->flush();
            return false;
        }

        $affected = $this->db->query(
            "UPDATE ?# SET `status` = ?, `is_done` = 1, `last_update_time` = ?d WHERE `id` = ?d AND `is_done` = 0",
            $this->table,
            $status,
            time(),
            $this->id
        );
        if ((int)$affected !== 1) {
            $this->resetChanges(); // Другой процесс уже обработал этот платёж - ничего не перезаписываем
            return false;
        }
        // Приводим модель к тому, что уже записано в БД (иначе __destruct сохранил бы старые значения)
        $this->status  = $status;
        $this->is_done = true;
        $this->flush();

        $handler = Config(['pay_system', $status === self::STATUS_PAID ? 'on_paid' : 'on_failed']);
        if (is_callable($handler)) {
            call_user_func($handler, $this);
        }
        return true;
    }

    // Спросить статус у банка и применить его
    public function checkStatusInBank(): string
    {
        if ($this->isExists() && !$this->is_done && $this->transfer_id !== '') {
            $this->applyStatus(PaySystem::getInstance()->getPaymentStatus($this->transfer_id));
        }
        return $this->status;
    }
}
