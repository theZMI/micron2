<?php

class TimerModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'value'            => 'int',
            'last_update_time' => 'int',
            'create_time'      => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `user_id` INT DEFAULT NULL,
              `value` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('timer', $id);
    }
}