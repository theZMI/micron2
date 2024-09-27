<?php

class ParamModel extends \Models\ModelExtends
{
    const TYPE_STRING = 10;
    const TYPE_IMAGE = 20;
    const TYPE_NUMBER = 30;
    const TYPE_DATE = 40;
    const TYPE_TIME = 50;

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `type` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('params', $id);
    }

    public static function getTypes()
    {
        return [
            self::TYPE_STRING => 'Текст',
            self::TYPE_IMAGE  => 'Изображение',
            self::TYPE_NUMBER => 'Число',
            self::TYPE_DATE   => 'Дата',
            self::TYPE_TIME   => 'Время'
        ];
    }

    public function __get($key)
    {
        if ($key === 'type_name') {
            return self::getTypes()[$this->type];
        }
        return parent::__get($key);
    }
}