<?php

/**
 * Простые миграции БД
 *
 * Каждая миграция - файл core/migrations/NNNN_описание.php, который возвращает function ($db) { ... }.
 * Применённые миграции записываются в таблицу migrations. Новые миграции применяются автоматически при первом
 * запросе к сайту после обновления кода (см. core/init/db.php), а так же их можно запустить из админки (_admin/migrate).
 * Миграции должны быть идемпотентными (проверяйте существование таблиц/колонок через tableExists/columnExists)
 */
class Migrator
{
    private static function dir(): string
    {
        return BASEPATH . 'core/migrations/';
    }

    // Файл-метка с именем последней применённой миграции, чтобы не ходить в БД на каждый запрос
    private static function markerFile(): string
    {
        return BASEPATH . 'tmp/migrations_applied.txt';
    }

    public static function all(): array
    {
        $files = glob(self::dir() . '*.php') ?: [];
        sort($files);
        return array_map(fn($file) => basename($file, '.php'), $files);
    }

    private static function db()
    {
        $db = GetDefaultDb();
        $db->query(
            "CREATE TABLE IF NOT EXISTS `migrations` (
                `name` VARCHAR(255) NOT NULL,
                `applied_time` INT NOT NULL,
                PRIMARY KEY (`name`)
            ) ENGINE = InnoDB"
        );
        return $db;
    }

    public static function pending(): array
    {
        $applied = self::db()->selectCol("SELECT `name` FROM `migrations`");
        return array_values(array_diff(self::all(), $applied));
    }

    // Применяет все новые миграции и возвращает список применённых
    public static function run(): array
    {
        $db   = self::db();
        $done = [];
        foreach (self::pending() as $name) {
            $migration = require self::dir() . "{$name}.php";
            $migration($db);
            $db->query("INSERT INTO `migrations` SET `name` = ?, `applied_time` = ?d", $name, time());
            $done[] = $name;
        }

        $all = self::all();
        FileSys::writeFile(self::markerFile(), (string)end($all));
        return $done;
    }

    public static function runIfNeeded(): void
    {
        $all    = self::all();
        $last   = (string)end($all);
        $marker = is_readable(self::markerFile()) ? trim((string)file_get_contents(self::markerFile())) : '';
        if ($last === '' || $marker === $last) {
            return;
        }
        self::run();
    }

    public static function tableExists($db, string $table): bool
    {
        return (bool)$db->selectCell(
            "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?",
            $table
        );
    }

    public static function columnExists($db, string $table, string $column): bool
    {
        return (bool)$db->selectCell(
            "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
            $table,
            $column
        );
    }
}
