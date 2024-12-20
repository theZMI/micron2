<?php

class ParamModel extends \Models\ModelExtends
{
    const TYPE_STRING   = 10;
    const TYPE_IMAGE    = 20;
    const TYPE_NUMBER   = 30;
    const TYPE_DATETIME = 40;
    const TYPE_TIME     = 50;

    const STATUS_VISIBLE = 0;
    const STATUS_HIDDEN = 1;

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `type` INT DEFAULT NULL,
              `status` INT DEFAULT NULL,
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
            self::TYPE_STRING   => 'Текст',
            self::TYPE_IMAGE    => 'Изображение',
            self::TYPE_NUMBER   => 'Число',
            self::TYPE_DATETIME => 'Дата',
            self::TYPE_TIME     => 'Время',
        ];
    }

    public function getValueFieldName()
    {
        if (in_array($this->type, [self::TYPE_NUMBER, self::TYPE_DATETIME, self::TYPE_TIME])) {
            return 'value_as_number';
        }
        return 'value_as_string';
    }

    public function __get($key)
    {
        if ($key === 'type_name') {
            return self::getTypes()[$this->type];
        }
        return parent::__get($key);
    }

    public function getList($page = self::PAGE_ALL)
    {
        $list = parent::getList($page);
        return array_filter($list, fn($v) => $v->status !== self::STATUS_HIDDEN);
    }

    public function delete()
    {
        $isParamUsed = count( (new ShiftParamModel())->find(['param_id' => $this->id]) );
        if ($isParamUsed) {
            $this->status = self::STATUS_HIDDEN;
            return true;
        }
        return parent::delete();
    }

    public function getDataToApi()
    {
        return array_merge(
            $this->getData(),
            [
                'type_name' => $this->type_name
            ]
        );
    }
}