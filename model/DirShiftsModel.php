<?php

class DirShiftsModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'name'             => 'string',
            'is_template'      => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_template` INT DEFAULT '0',
              `create_time` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
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
            return count($shifts)
                ? $shifts[array_key_first($shifts)]->start_time
                : 0;
        }
        elseif ($key === 'end_time') {
            $shifts = $this->shifts;
            return count($shifts)
                ? $shifts[array_key_first($shifts)]->end_time
                : 0;
        }
        elseif ($key === 'user_ids') {
            return array_map(fn($shift) => +$shift->user_id, $this->shifts);
        }
        elseif ($key === 'users') {
            return array_map(fn($id) => new UserModel($id), $this->user_ids);
        }
        return parent::__get($key);
    }

    public function find($params)
    {
        $is_template = boolval($params['is_template'] ?? false);
        unset($params['is_template']);

        if (count($params) === 0) { // Если единственный параметр поиска это is_template
            return array_filter(
                parent::getList(),
                fn($v) => +$v->is_template === +$is_template
            );
        }
        if (isset($params['user_id'])) {
            return array_filter(
                parent::getList(),
                fn($v) => in_array(+$params['user_id'], $v->user_ids) && +$v->is_template === +$is_template
            );
        }
        return [];
    }

    public function getList($page = self::PAGE_ALL)
    {
        return $this->find(['is_template' => false]);
    }

    public function delete()
    {
        $shifts = $this->shifts;
        array_walk($shifts, fn($v) => $v->delete());
        return parent::delete();
    }
}