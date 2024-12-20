<?php

class UserDepartmentModel extends \Models\ModelExtends
{
    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `user_id` INT DEFAULT NULL,
              `department_id` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('user_departments', $id);
    }

    public function find($params)
    {
        $ids = [];
        if (isset($params['department_id'])) {
            $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `department_id` = ?d", $this->table, $params['department_id']);
        } elseif ($params['user_id']) {
            $ids = $this->db->selectCol("SELECT `id` FROM ?# WHERE `user_id` = ?d", $this->table, $params['user_id']);
        }
        $ids = empty($ids) ? [] : $ids;
        return array_map(fn($id) => new self($id), $ids);
    }
}