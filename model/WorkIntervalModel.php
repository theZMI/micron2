<?php

class WorkIntervalModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'user_id'          => 'int',
            'start'            => 'int',
            'stop'             => 'int',
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
              `start` INT DEFAULT NULL,
              `stop` INT DEFAULT NULL,
              `last_update_time` INT DEFAULT NULL,
              `create_time` INT DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('work_intervals', $id);
    }

    public function find($params)
    {
        $ids = [];
        $user_id = $params['user_id'] ?? 0;
        $from = $params['from'] ?? 0;
        if ($user_id) {
            if ($from === 'active_day') {
                $midnight = strtotime(date('d-m-Y 00:00:00'));
                $ids      = $this->db->selectCol(
                    "SELECT
                        `id`
                    FROM ?#
                    WHERE
                        `user_id` = ?d
                        AND ( 
                            `start` >= ?d # Которые начали тикать после полночи
                            OR ( # Те которые включили до полночи, а выключили после полночи
                                `start` < ?d AND `stop` > ?d
                            )
                            OR ( # Те которые включили до полночи и они всё ещё тикают
                                `start` < ?d AND `stop` = 0
                            ) 
                        )
                    ",
                    $this->table,
                    $user_id,
                    $midnight,
                    $midnight,
                    $midnight,
                    $midnight
                );
            } else {
                $ids = $this->db->selectCol(
                    "SELECT `id` FROM ?# WHERE `user_id` = ?d { AND `start` >= ?d }",
                    $this->table,
                    $params['user_id'],
                    $params['from'] ? +$params['from'] : DBSIMPLE_SKIP
                );
            }
        }
        $ids = empty($ids) ? [] : $ids;
        return array_map(fn($id) => new self($id), $ids);
    }
}