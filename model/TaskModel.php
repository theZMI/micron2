<?php

class TaskModel extends SiteModel
{
    const STATUS_CREATED = 0;
    const STATUS_DONE    = 2;
    const STATUS_FAILED  = -1;

    public function scheme()
    {
        return [
            'id'                       => 'int',
            'task'                     => 'string',
            'description'              => 'string',
            'user_comment'             => 'string',
            'is_user_comment_required' => 'bool',
            'photo_1'                  => 'string',
            'is_photo_1_required'      => 'bool',
            'photo_2'                  => 'string',
            'is_photo_2_required'      => 'bool',
            'photo_3'                  => 'string',
            'is_photo_3_required'      => 'bool',
            'photo_4'                  => 'string',
            'is_photo_4_required'      => 'bool',
            'photo_5'                  => 'string',
            'is_photo_5_required'      => 'bool',
            'status'                   => 'int',
            'last_status_change_time'  => 'int',
            'deadline_time'            => 'int',
            'done_time'                => 'int',
            'shift_id'                 => 'int',
            'user_id'                  => 'int',
            'create_time'              => 'int',
            'last_update_time'         => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
              `id` INT NOT NULL AUTO_INCREMENT,
              `task` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `user_comment` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_user_comment_required` INT DEFAULT NULL,
              `photo_1` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_photo_1_required` INT DEFAULT NULL,
              `photo_2` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_photo_2_required` INT DEFAULT NULL,
              `photo_3` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_photo_3_required` INT DEFAULT NULL,
              `photo_4` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_photo_4_required` INT DEFAULT NULL,
              `photo_5` VARCHAR(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
              `is_photo_5_required` INT DEFAULT NULL,
              `status` INT DEFAULT NULL,
              `last_status_change_time` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              `deadline_time` INT DEFAULT NULL,
              `done_time` INT DEFAULT NULL,
              `shift_id` INT DEFAULT NULL,
              `user_id` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('tasks', $id);
    }

    public function statuses()
    {
        return [
            self::STATUS_CREATED => ['name' => 'В работе...', 'label' => '<span class="task-status-label badge rounded-pill ps-2 pe-2 text-bg-secondary">В работе...</span>'],
            self::STATUS_DONE    => ['name' => 'Завершена',   'label' => '<span class="task-status-label badge rounded-pill ps-2 pe-2 text-bg-success">Завершена</span>'],
            self::STATUS_FAILED  => ['name' => 'Провалена',   'label' => '<span class="task-status-label badge rounded-pill ps-2 pe-2 text-bg-danger">Провалена</span>'],
        ];
    }

    public function __get($key)
    {
        return match ($key) {
            'is_done'       => +$this->status === self::STATUS_DONE,
            'shift'         => new ShiftModel($this->shift_id),
            'user'          => new UserModel($this->shift->user_id),
            'status_label'  => $this->statuses()[+$this->status]['label'],
            default         => parent::__get($key),
        };
    }

    public function __set($key, $value)
    {
        switch ($key) {
            case 'status':
                $this->last_status_change_time = time();
                if (+$value === self::STATUS_DONE) {
                    $this->done_time = time();
                }
            default:
                parent::__set($key, $value);
        }
    }

    public function count($status = null)
    {
        return +$this->db->selectCell(
            "SELECT COUNT(*) FROM ?# { WHERE `status` = ?d }",
            $this->table,
            $status ?: DBSIMPLE_SKIP
        );
    }

    public function find($params)
    {
        $ret = [];
        if (isset($params['shift_id'])) {
            $shift_id = $params['shift_id'];
            $ids      = $this->db->selectCol("SELECT `id` FROM ?# WHERE `shift_id` = ?d", $this->table, $shift_id);
            $ids      = empty($ids) ? [] : $ids;
            $ret      = [];
            foreach ($ids as $id) {
                $ret[$id] = new self($id);
            }
        }
        return $ret;
    }

    public function getDataToApi()
    {
        return array_merge(
            $this->getData(),
            [
                'shift'        => $this->shift->getData(), // Здесь специально не getDataToApi
                'status_label' => $this->status_label,
            ]
        );
    }

    public function flush()
    {
        if ($this->hasChanges()) {
            $this->last_update_time = time();
        }
        return parent::flush();
    }
}