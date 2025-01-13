<?php

class TestSchemeModel extends ModelWithScheme
{
    public function scheme()
    {
        return [
            'id'            => 'int',
            'int_data_1'    => 'int',
            'string_data_1' => 'string',
            'string_data_2' => 'string',
            'bool_data_1'   => 'bool',
            'float_data_1'  => 'float',
            'create_time'   => 'int',
        ];
    }

    public function __construct($id = null)
    {
        parent::__construct('test_scheme', $id);
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `int_data_1` INT DEFAULT NULL,
              `string_data_1` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `string_data_2` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `bool_data_1` BOOLEAN DEFAULT FALSE,
              `float_data_1` FLOAT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }
}