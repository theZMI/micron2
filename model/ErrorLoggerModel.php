<?php

class ErrorLoggerModel extends SiteModel
{
    const PAGE_LIMIT = 10;

    public function scheme()
    {
        return [
            'id'               => 'int',
            '_get'             => 'string',
            '_post'            => 'string',
            '_cookie'          => 'string',
            '_session'         => 'string',
            '_server'          => 'string',
            '_files'           => 'string',
            'backtrace'        => 'string',
            'sql'              => 'string',
            'ip'               => 'string',
            'browser'          => 'string',
            'browser_version'  => 'string',
            'platform'         => 'string',
            'g_config'         => 'string',
            'g_lang'           => 'string',
            'g_user'           => 'string',
            'errno'            => 'int',
            'errstr'           => 'string',
            'errfile'          => 'string',
            'errline'          => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `_get` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `_post` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `_cookie` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `_session` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `_server` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `_files` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `backtrace` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `sql` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `ip` VARCHAR(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `browser` VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `browser_version` VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `platform` VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `g_config` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `g_lang` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `g_user` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `errno` INT DEFAULT NULL,
                `errstr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `errfile` VARCHAR(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
                `errline` INT DEFAULT NULL,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('error_logger', $id);
    }

    public function count()
    {
        return $this->db->selectCell("SELECT COUNT(*) FROM ?#", $this->table);
    }

    public function clear()
    {
        $this->db->query("TRUNCATE TABLE ?#", $this->table);
    }
}
