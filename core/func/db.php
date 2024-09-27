<?php

function GetDefaultDb()
{
    $dbs = Dbs::getInstance()->getDatabases();
    if (isset($dbs->db)) {
        return $dbs->db;
    }
    throw new Exception("Can't get default database");
}