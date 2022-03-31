<?php

ini_set('max_execution_time', '-1');
ini_set('memory_limit', '-1');

$dsn        = \Nyholm\Dsn\DsnParser::parse($g_config['dbSimple']['databases']['db']['dsn']);
$dbDumpsDir = BASEPATH . 'db_backups/';
FileSys::MakeDir($dbDumpsDir);

BackupTables($dbDumpsDir, $dsn->getHost(), $dsn->getUser(), $dsn->getPassword(), trim($dsn->getPath(), '/'));
