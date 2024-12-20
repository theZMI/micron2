<?php

class ShiftParamModel extends \Models\ModelExtends
{
    public function __construct($id = null)
    {
        parent::__construct('shift_params', $id);
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `shift_id` INT DEFAULT NULL,
              `param_id` INT DEFAULT NULL,
              `value_as_string` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `value_as_number` FLOAT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __get($key)
    {
        if ($key === 'param') {
            return new ParamModel($this->param_id);
        }
        elseif ($key === 'value') {
            $valueField = $this->param->getValueFieldName();
            return $this->$valueField;
        }
        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if ($key === 'value') {
            $this->value_as_string = (string)$value;
            $this->value_as_number = (float)$value;
        }
        return parent::__set($key, $value);
    }

    public function find($params)
    {
        $ids = [];
        if (isset($params['shift_id'])) {
            $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `shift_id` = ?d", $this->table, $params['shift_id']);
        }
        if (isset($params['param_id'])) {
            $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `param_id` = ?d", $this->table, $params['param_id']);
        }
        return array_map(fn($id) => new self($id), $ids);
    }

    public function getDataToApi()
    {
        return array_merge(
            $this->getData(),
            [
                'value' => $this->value,
                'param' => $this->param->getDataToApi()
            ]
        );
    }
}