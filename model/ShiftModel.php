<?php

class ShiftModel extends SiteModel
{
    const STATUS_CREATED = 0;
    const STATUS_DONE    = 2;

    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'start_time'       => 'int',
            'end_time'         => 'int',
            'status'           => 'int',
            'create_time'      => 'int',
            'creator_id'       => 'int',
            'last_update_time' => 'int',
            'dir_id'           => 'int',
        ];
    }

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
              `creator_id` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              `dir_id` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shifts', $id);
    }

    public function statuses($status = null)
    {
        $all = [
            self::STATUS_CREATED => ['name' => 'В работе', 'label' => '<span class="badge text-white text-bg-secondary">В работе</span>'],
            self::STATUS_DONE    => ['name' => 'Закрыта', 'label' => '<span class="badge text-white text-bg-success">Закрыта</span>'],
        ];
        return is_null($status) ? $all : $all[$status];
    }

    public function __get($key)
    {
        $calcProgress = function () {
            static $cache = [];
            if (isset($cache[$this->id])) {
                return $cache[$this->id];
            }

            $tasks  = $this->tasks;
            $total  = count($tasks);
            $done   = array_reduce($tasks, fn($done, $v) => $done+=intval($v->is_done), 0);
            $failed = array_reduce($tasks, fn($failed, $v) => $failed+=intval($v->is_failed), 0);

            return $cache[$this->id] = [
                'total'          => $total,
                'done'           => intval($done),
                'failed'         => intval($failed),
                'percent_done'   => $total ? 100 * ($done / $total) : 0,
                'percent_failed' => $total ? 100 * ($failed / $total) : 0,
            ];
        };

        return match ($key) {
            'tasks'        => (new TaskModel())->find(['shift_id' => $this->id]),
            'params'       => (new ShiftParamModel())->find(['shift_id' => $this->id]),
            'dir'          => (new DirShiftsModel($this->dir_id)),
            'is_template'  => $this->dir->is_template,
            'progress'     => $calcProgress(),
            'user'         => new UserModel($this->user_id),
            'status_name'  => $this->statuses(+$this->status)['name'],
            'status_label' => $this->statuses(+$this->status)['label'],
            'creator'      => new AdminModel($this->creator_id),
            default        => parent::__get($key)
        };
    }

    public function __set($key, $value)
    {
        // Если мы закрываем сменю, то проходимся по всем задачам и те которые ещё не выполнены отмечаем как проваленные
        if ($key === 'status' && +$value === self::STATUS_DONE) {
            $tasks = $this->tasks;
            array_walk($tasks, function ($task) {
                if ($task->status === TaskModel::STATUS_DONE) {
                    return;
                }
                $task->status = TaskModel::STATUS_FAILED;
            });
        }
        return parent::__set($key, $value);
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
                'tasks'       => array_values( array_map(fn($task) => $task->getDataToApi(), $this->tasks) ),
                'params'      => array_values( array_map(fn($param) => $param->getDataToApi(), $this->params) ),
                'dir'         => $this->dir->getData(), // Здесь специально не getDataToApi, чтобы не получить цикл, если вдруг напишу метод DirShiftsModel::getDataToApi. (цикл возникнет так как getDataToApi в dir-е будет возвращать список всех смен, а те снова указывают инфу о папке и так по циклу)
                'is_template' => $this->dir->is_template,
            ]
        );
    }
}