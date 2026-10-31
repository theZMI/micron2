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
