<?php

class ShopOrderModel extends \Models\ModelExtends implements IShopOrderModel
{
    public function createTable()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `user_id` INT DEFAULT NULL,
                `currency` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_orders', $id);
    }

    public function __get($key)
    {
        switch ($key) {
            case 'items':
                $ret = (new ShopOrderItemModel())->getListByShopOrderID($this->id);
                break;
            case 'total_amount':
                $ret = array_reduce(
                    $this->items,
                    fn($total, $item) => $total += $item->amount
                );
                break;
            case 'payment':
                $ret = new PaymentModel(
                    PaymentModel::getIdByShopOrderId($this->id)
                );
                break;
            default:
                $ret = parent::__get($key);
        }
        return $ret;
    }
}