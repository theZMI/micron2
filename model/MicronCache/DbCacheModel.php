<?php

namespace MicronCache;

class DbCacheModel extends \Models\ModelExtends implements ICache
{
    protected function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `key` VARCHAR(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL, 
                `data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL, 
                `create_time` INT NULL DEFAULT NULL, 
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('micron_db_cache', $id);
    }

    public function has(string $key): bool
    {
        return (bool)$this->db->selectCell("SELECT `id` FROM ?# WHERE `key` = ?", $this->table, $key);
    }

    public function get(string $key): mixed
    {
        $id    = $this->db->selectCell("SELECT `id` FROM ?# WHERE `key` = ? ORDER BY `create_time` DESC LIMIT 1", $this->table, $key);
        $model = new self($id);
        return $model->isExists() ? json_decode($model->data, true) : null;
    }

    public function set(string $key, mixed $data): int
    {
        $model       = new self();
        $model->key  = $key;
        $model->data = json_encode($data);
        return (int)$model->flush();
    }
}