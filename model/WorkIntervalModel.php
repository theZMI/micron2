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
        if ($params['user_id']) {
            $ids = $this->db->selectCol(
                "SELECT `id` FROM ?# WHERE `user_id` = ?d { AND `start` >= ?d }",
                $this->table,
                $params['user_id'],
                isset($params['from']) ? +$params['from'] : DBSIMPLE_SKIP
            );
        }
        $ids = empty($ids) ? [] : $ids;
        return array_map(fn($id) => new self($id), $ids);
    }
}