<?php

function GetDefaultDb()
{
    $dbs = Dbs::getInstance()->getDatabases();
    if (isset($dbs->db)) {
        return $dbs->db;
    }
    throw new Exception("Can't get default database");
}

/**
 * Помечает запрос для кеширования (см. pCacheFunc в core/config/db.php)
 * Пример: $db->selectCell(AddDbCache("SELECT COUNT(*) FROM `users`"));
 */
function AddDbCache(string $query, int $ttl = MyDataBaseCache::CACHE_TIME): string
{
    return MyDataBaseCache::cacheTag($query, $ttl);
}


/**
 * Таблица SQL-запросов для debug-панели и лога ошибок.
 * MyDataBaseLog::render() выводит запросы как есть, а в них бывают данные пользователя, поэтому экранируем их заранее (защита от XSS)
 */
function RenderDbLog(array $log): string
{
    foreach ($log as &$row) {
        if (is_array($row)) {
            array_walk_recursive($row, function (&$v) {
                $v = is_string($v) ? htmlspecialchars($v) : $v;
            });
        }
    }
    unset($row);
    return $log ? MyDataBaseLog::render($log) : ''; // Пустой массив не передаём: render() тогда возьмёт текущий (неэкранированный) лог
}


/**
 * Выполняет $fn($db) в транзакции: commit, а при исключении - rollback и исключение дальше.
 * Внимание: модели при первом new в процессе выполняют CREATE TABLE IF NOT EXISTS, а любой DDL в MySQL неявно
 * завершает транзакцию (дальше всё пишется сразу и rollback не сработает). Создавайте модели до DbTransaction()
 */
function DbTransaction(callable $fn)
{
    $db = GetDefaultDb();
    $db->transaction();
    try {
        $ret = $fn($db);
        $db->commit();
        return $ret;
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}
