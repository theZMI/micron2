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
        return match ($key) {
            'tasks'       => (new TaskModel())->find(['shift_id' => $this->id]),
            'params'      => (new ShiftParamModel())->find(['shift_id' => $this->id]),
            'dir'         => (new DirShiftsModel($this->dir_id)),
            'is_template' => $this->dir->is_template,
            default       => parent::__get($key)
        };
    }

    public function find($params)
    {
        $params['is_template'] = $params['is_template'] ?? false;

        if (isset($params['dir_id'])) {
            $dir_id = $params['dir_id'];
            $ids    = $this->db->selectCol("SELECT `id` FROM ?# WHERE `dir_id` = ?d", $this->table, $dir_id);
            $ids    = empty($ids) ? [] : $ids;
            return array_map(fn($id) => new self($id), $ids);
        }
        if (isset($params['user_id'])) {
            $user_id = $params['user_id'];
            $ids     = $this->db->selectCol("SELECT `id` FROM ?# WHERE `user_id` = ?d", $this->table, $user_id);
            $ids     = empty($ids) ? [] : $ids;
            $ret     = array_map(fn($id) => new self($id), $ids);
            return array_filter($ret, fn($v) => +$v->is_template === +$params['is_template']); // Если указано is_template=true, то работает только по шаблонам, иначе по сменам
        }

        return [];
    }

    public function findOne($params)
    {
        if (isset($params['dir_id']) && isset($params['user_id'])) {
            $dir_id   = +$params['dir_id'];
            $user_id  = +$params['user_id'];
            $shift_id = $this->db->selectCell(
                "SELECT `id` FROM ?# WHERE `dir_id` = ?d AND `user_id` = ?d",
                $this->table,
                $dir_id,
                $user_id
            );
            return $shift_id ? new self($shift_id) : null;
        }
        return null;
    }

    public function delete()
    {
        $tasks = $this->tasks;
        array_walk($tasks, fn($v) => $v->delete());
        $params = $this->params;
        array_walk($params, fn($v) => $v->delete());
        return parent::delete();
    }

    public function getDataToApi()
    {
        return array_merge(
            $this->getData(),
            [
                'tasks'       => array_map(fn($task) => $task->getDataToApi(), $this->tasks),
                'param'       => array_map(fn($param) => $param->getDataToApi(), $this->params),
                'dir'         => $this->dir->getData(), // Здесь специально не getDataToApi так как оно будет возвращать shifts, что приведёт к зацикливанию
                'is_template' => $this->dir->is_template,
            ]
        );
    }
}