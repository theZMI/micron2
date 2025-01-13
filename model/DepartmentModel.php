<?php

class DepartmentModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'department'       => 'string',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `department` VARCHAR(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('departments', $id);
    }

    public function __get($key)
    {
        if ($key === 'users') {
            $conn = (new UserDepartmentModel())->find(['department_id' => $this->id]);
            $user_ids = array_unique( array_map(fn($v) => $v->user_id, $conn) );
            return array_map(fn($user_id) => new UserModel($user_id), $user_ids);
        }
        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        return parent::__set($key, $value);
    }

    public function delete()
    {
        $conns = (new UserDepartmentModel())->find(['department_id' => $this->id]);
        array_walk($conns, fn($v) => $v->delete());
        return parent::delete();
    }
}