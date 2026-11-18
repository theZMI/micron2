<?php

Dbs::getInstance(Config('db_simple'));

// На ошибку «таблица не существует» сбрасываем список созданных моделями таблиц (см. lib/DbTablesCache.php)
if (Config(['db_simple', 'logDbError'])) {
    MyDataBaseLog::setFuncOnError(['DbTablesCache', 'onDbError']);
}
