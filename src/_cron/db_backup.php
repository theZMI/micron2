<?php

ini_set('max_execution_time', '-1');
ini_set('memory_limit', '-1');

$dsn = \Nyholm\Dsn\DsnParser::parse(Config('db_simple')['databases']['db']['dsn']);
$dbDumpsDir = BASEPATH . 'db_backups/';
FileSys::MakeDir($dbDumpsDir);

backup_tables($dbDumpsDir, $dsn->getHost(), $dsn->getUser(), $dsn->getPassword(), trim($dsn->getPath(), '/'));