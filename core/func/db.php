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
