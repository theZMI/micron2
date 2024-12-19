<?php

class ShiftModel extends \Models\ModelExtends
{
    const STATUS_CREATED = 0;
    const STATUS_DONE    = 2;

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `user_id` INT DEFAULT NULL,
              `start_time` INT DEFAULT NULL,
              `end_time` INT DEFAULT NULL,
              `status` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              `dir_id` INT DEFAULT NULL,
              `work_time` INT DEFAULT NULL,
              `_work_intervals` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shifts', $id);
    }

    public function __get($key)
    {
        if ($key === 'tasks') {
            return (new TaskModel())->find(['shift_id' => $this->id]);
        }
        if ($key === 'params') {
            return (new ShiftParamModel())->find(['shift_id' => $this->id]);
        }
        if ($key === 'is_template') {
            return (new DirShiftsModel($this->dir_id))->is_template;
        }
        return parent::__get($key);
    }

    public function find($params)
    {
        if (isset($params['dir_id'])) {
            $dir_id = $params['dir_id'];
            $ids    = $this->db->selectCol("SELECT `id` FROM ?# WHERE `dir_id` = ?d", $this->table, $dir_id);
            $ids    = empty($ids) ? [] : $ids;
            $ret    = [];
            foreach ($ids as $id) {
                $ret[$id] = new self($id);
            }
            return $ret;
        }
        if (isset($params['user_id'])) {
            $user_id = $params['user_id'];
            $ids     = $this->db->selectCol("SELECT `id` FROM ?# WHERE `user_id` = ?d", $this->table, $user_id);
            $ids     = empty($ids) ? [] : $ids;
            $ret     = [];
            foreach ($ids as $id) {
                $one = new self($id);
                if (isset($params['is_template'])) {
                    if (+$one->is_template !== +$params['is_template']) {
                        continue;
                    }
                }
                $ret[$id] = $one;
            }
            return $ret;
        }

        return [];
    }

    public function findOne($params)
    {
        if (isset($param['dir_id'])) {
            $dir_id = $params['dir_id'];
            if (isset($params['user_id'])) {
                $user_id = $params['user_id'];

                $shift_id = $this->db->selectCell(
                    "SELECT `id` FROM ?# WHERE `dir_id` = ?d AND `user_id` = ?d",
                    $this->table,
                    $dir_id,
                    $user_id
                );
                return $shift_id ? new self($shift_id) : null;
            }
        }
        return null;
    }

    public function delete()
    {
        foreach ($this->tasks as $task) {
            $task->delete();
        }
        return parent::delete();
    }
}