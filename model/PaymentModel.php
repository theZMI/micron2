<?php

/**
 * @property int $user_id
 * @property int $shop_order_id
 * @property string $transfer_id
 * @property float $amount
 * @property string $currency
 * @property string $status
 * @property int $pay_time
 * @property string $pay_link
 */
class PaymentModel extends \Models\ModelExtends implements IPaymentModel
{
    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT DEFAULT NULL,
                `shop_order_id` INT DEFAULT NULL,
                `transfer_id` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `amount` FLOAT DEFAULT NULL,
                `currency` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `status` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `pay_time` INT DEFAULT NULL,
                `pay_link` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('payments', $id);
    }

    private function _getIdByShopOrderId(int $shop_order_id): int
    {
        return +$this->db->select->selectCell("SELECT * FROM ?# WHERE `shop_order_id` = ?d", $this->table, $shop_order_id);
    }

    public static function getIdByShopOrderId(int $shop_order_id): int
    {
        return (new self())->_getIdByShopOrderId($shop_order_id);
    }

    // Процессы которые нужно выполнить когда юзер успешно оплатил заказ (товары там уменьшить в магазине, письма разослать о заказе и пр.)
    private function onPaid()
    {
    }

    // Процессы если платёж завершился неудачно
    private function onFailed()
    {
    }

    public function __set($key, $value)
    {
        switch ($key) {
            case 'status':
                match ($value) {
                    'paid'   => $this->onPaid(),
                    'failed' => $this->onFailed()
                };
                parent::__set($key, $value);
                break;
            default:
                parent::__set($key, $value);
        }
    }

    public function __get($key)
    {
        switch ($key) {
            case 'is_done':
                $ret = in_array($this->status, ['paid', 'failed']);
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }

    public function getWaitingList()
    {
        $ids = $this->db->selectCell("SELECT `id` FROM ?# WHERE `status` = 'waiting'", $this->table) ?: [];
        return array_map(fn($id) => new self($id), $ids);
    }
}