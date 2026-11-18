<?php

/**
 * Список таблиц, которые модели уже создали, - между запросами.
 *
 * Модели при первом new в процессе выполняют CREATE TABLE IF NOT EXISTS (см. createTable()), и библиотека моделей
 * помнит это только до конца запроса. Из-за этого на каждой странице шло по CREATE TABLE на каждую модель.
 * Здесь список хранится в файле в tmp/: если таблица в нём есть, createTable() не вызывается.
 *
 * Файл свой для каждой БД (хост + порт + имя базы). Сбрасывается сам:
 * - после применения миграций (они могут удалить или переименовать таблицу);
 * - при ошибке «таблица не существует» (1146) - например, БД пересоздали, а файл остался.
 * Сбросить вручную - удалить tmp/db_tables_created_*.json.
 */
class DbTablesCache
{
    private static ?array $tables = null;

    private static function file(): string
    {
        // В имени файла - только хеш адреса БД, без логина и пароля из DSN
        $dsn = parse_url((string)Env('DATABASE_DSN')) ?: [];
        $key = ($dsn['host'] ?? '') . ':' . ($dsn['port'] ?? '') . ($dsn['path'] ?? '');
        return BASEPATH . 'tmp/db_tables_created_' . substr(sha1($key), 0, 12) . '.json';
    }

    private static function load(): array
    {
        if (self::$tables === null) {
            $data         = is_readable(self::file()) ? json_decode((string)file_get_contents(self::file()), true) : null;
            self::$tables = is_array($data) ? array_fill_keys($data, true) : [];
        }
        return self::$tables;
    }

    public static function has(string $table): bool
    {
        return isset(self::load()[$table]);
    }

    public static function add(string $table): void
    {
        if (self::has($table)) {
            return;
        }
        self::$tables[$table] = true;
        FileSys::writeFile(self::file(), json_encode(array_keys(self::$tables)));
    }

    public static function reset(): void
    {
        self::$tables = [];
        if (is_file(self::file())) {
            unlink(self::file());
        }
    }

    /**
     * Говорит библиотеке моделей (\Models\ModelOptimized), что таблица в этом запросе уже создана,
     * чтобы её конструктор не вызывал createTable(). Список у неё private static, поэтому через Reflection.
     */
    public static function markCreatedInRequest(string $table): void
    {
        static $prop = null;
        if ($prop === null) {
            $prop = new ReflectionProperty(\Models\ModelOptimized::class, '_listCreatedTables');
        }
        $list         = $prop->getValue();
        $list[$table] = true;
        $prop->setValue(null, $list);
    }

    /**
     * Обработчик ошибок БД (ставится в core/init/db.php поверх стандартного Dbs::dbSimpleError):
     * на «таблица не существует» сбрасывает список, чтобы на следующем запросе модели снова создали таблицы
     */
    public static function onDbError($message, $info): void
    {
        if ((int)($info['code'] ?? 0) === 1146) {
            self::reset();
        }
        Dbs::dbSimpleError($message, $info);
    }
}
