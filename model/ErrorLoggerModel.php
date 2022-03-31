<?php

class ErrorLoggerModel extends \Models\ModelExtends
{
    public function __construct($id = null)
    {
        parent::__construct('error_logger', $id);
    }

    public function createTable()
    {
        $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `create_time` int(11) DEFAULT NULL,
                `_get` longtext CHARACTER SET utf8,
                `_post` longtext CHARACTER SET utf8,
                `_cookie` longtext CHARACTER SET utf8,
                `_session` longtext CHARACTER SET utf8,
                `_server` longtext CHARACTER SET utf8,
                `_files` longtext CHARACTER SET utf8,
                `backtrace` longtext CHARACTER SET utf8,
                `sql` longtext CHARACTER SET utf8,
                `ip` int(11) DEFAULT NULL,
                `browser` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `browser_version` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `platform` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `aol` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `g_config` longtext CHARACTER SET utf8,
                `g_lang` longtext CHARACTER SET utf8,
                `g_user` longtext CHARACTER SET utf8,
                `errno` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `errstr` text CHARACTER SET utf8,
                `errfile` varchar(2048) CHARACTER SET utf8 DEFAULT NULL,
                `errline` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1",
            $this->table
        );
    }

    public static function ipAddress()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = ip2long($_SERVER['HTTP_CLIENT_IP']);
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = ip2long($_SERVER['HTTP_X_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $ipaddress = ip2long($_SERVER['HTTP_X_FORWARDED']);
        } elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $ipaddress = ip2long($_SERVER['HTTP_FORWARDED_FOR']);
        } elseif (isset($_SERVER['HTTP_FORWARDED'])) {
            $ipaddress = ip2long($_SERVER['HTTP_FORWARDED']);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ipaddress = ip2long($_SERVER['REMOTE_ADDR']);
        } else {
            $ipaddress = -1;
        }

        return $ipaddress;
    }

    public function count()
    {
        return $this->db->selectCell("SELECT COUNT(*) FROM ?#", $this->table);
    }

    public function deleteAll()
    {
        $this->db->query("TRUNCATE TABLE ?#", $this->table);
    }
}
