<?php

class DirShiftsModel extends \Models\ModelExtends
{
    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_template` INT DEFAULT '0',
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('dir_shifts', $id);
    }

    public function __get($key)
    {
        if ($key === 'shifts') {
            return (new ShiftModel())->find(['dir_id' => $this->id]);
        }
        elseif ($key === 'start_time') {
            $shifts = $this->shifts;
            if (count($shifts)) {
                return $shifts[array_key_first($shifts)]->start_time;
            }
            return 0;
        }
        elseif ($key === 'end_time') {
            $shifts = $this->shifts;
            if (count($shifts)) {
                return $shifts[array_key_first($shifts)]->end_time;
            }
            return 0;
        }
        elseif ($key === 'user_ids') {
            $shifts   = $this->shifts;
            $user_ids = [];
            foreach ($shifts as $shift) {
                $user_ids[] = $shift->user_id;
            }
            return $user_ids;
        }
        elseif ($key === 'users') {
            return array_map(fn($id) => new UserModel($id), $this->user_ids);
        }
        return parent::__get($key);
    }

    public function findShift($params)
    {
        if (isset($params['user_id'])) {
            return (new ShiftModel())->findOne([
                'dir_id'  => $this->id,
                'user_id' => $params['user_id']
            ]);
        }
        return null;
    }

    public function delete()
    {
        foreach ($this->shifts as $shift) {
            $shift->delete();
        }
        return parent::delete();
    }
}