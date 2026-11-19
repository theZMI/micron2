<?php

/**
 * Категория товаров. Дерево - через parent_id (0 - верхний уровень), адрес - /shop/c/<slug>.
 * Товары подкатегорий показываются и в родительской категории
 */
class ShopCategoryModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'parent_id'        => 'int',
            'name'             => 'string',
            'slug'             => 'string',
            'description'      => 'string',
            'sort'             => 'int',
            'is_active'        => 'bool',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `parent_id` INT NOT NULL DEFAULT 0,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `slug` VARCHAR(255) NOT NULL DEFAULT '',
                `description` TEXT DEFAULT NULL,
                `sort` INT NOT NULL DEFAULT 0,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                UNIQUE KEY `slug` (`slug`),
                KEY `parent_id` (`parent_id`, `sort`),
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_categories', $id);
    }

    public function url(): string
    {
        return SiteRoot("shop/c/{$this->slug}");
    }

    /**
     * Все категории одним запросом (на запрос кешируется): [id => ['id', 'parent_id', 'name', 'slug', 'is_active', ...]].
     * Дерево и пути строятся из этого массива, без запросов на каждую категорию
     */
    public static function rows(bool $reset = false): array
    {
        static $rows = null;
        if ($rows === null || $reset) {
            new self(); // Таблица могла ещё не создаться
            $rows = [];
            foreach (GetDefaultDb()->select("SELECT `id`, `parent_id`, `name`, `slug`, `sort`, `is_active` FROM `shop_categories` ORDER BY `sort` ASC, `name` ASC") as $row) {
                $row['id']        = (int)$row['id'];
                $row['parent_id'] = (int)$row['parent_id'];
                $row['is_active'] = (bool)$row['is_active'];
                $rows[$row['id']] = $row;
            }
        }
        return $rows;
    }

    // Активные категории, у которых все родители тоже активны
    public static function activeRows(): array
    {
        $rows = self::rows();
        return array_filter($rows, function ($row) use ($rows) {
            for ($i = 0; $row && $i < 50; $i++) {
                if (!$row['is_active']) {
                    return false;
                }
                $row = $rows[$row['parent_id']] ?? null;
            }
            return true;
        });
    }

    // Дочерние категории: [row, ...]
    public static function children(int $parent_id, bool $onlyActive = true): array
    {
        $rows = $onlyActive ? self::activeRows() : self::rows();
        return array_values(array_filter($rows, fn($row) => $row['parent_id'] === $parent_id));
    }

    // id категории и всех её потомков
    public static function descendantIds(int $id): array
    {
        $ids   = [$id];
        $rows  = self::rows();
        $queue = [$id];
        while ($queue) {
            $parent = array_shift($queue);
            foreach ($rows as $row) {
                if ($row['parent_id'] === $parent && !in_array($row['id'], $ids, true)) {
                    $ids[]   = $row['id'];
                    $queue[] = $row['id'];
                }
            }
        }
        return $ids;
    }

    // Путь от корня до категории: [row, ...]
    public static function path(int $id): array
    {
        $rows = self::rows();
        $path = [];
        for ($row = $rows[$id] ?? null, $i = 0; $row && $i < 50; $row = $rows[$row['parent_id']] ?? null, $i++) {
            array_unshift($path, $row);
        }
        return $path;
    }

    // «Одежда / Футболки» - так категория пишется в импорте и в списках
    public static function pathName(int $id): string
    {
        return implode(' / ', array_column(self::path($id), 'name'));
    }

    // Список для <select>: [id => '— Футболки'] в порядке дерева
    public static function selectOptions(int $excludeId = 0): array
    {
        $ret  = [];
        $walk = function (int $parent_id, int $level) use (&$walk, &$ret, $excludeId) {
            foreach (self::children($parent_id, false) as $row) {
                if ($row['id'] === $excludeId) {
                    continue; // Нельзя сделать категорию дочерней самой себе
                }
                $ret[$row['id']] = str_repeat('— ', $level) . $row['name'];
                $walk($row['id'], $level + 1);
            }
        };
        $walk(0, 0);
        return $ret;
    }

    public static function findBySlug(string $slug): self
    {
        foreach (self::rows() as $row) {
            if ($row['slug'] === $slug) {
                return new self($row['id']);
            }
        }
        return new self();
    }

    public static function urlById(int $id): string
    {
        $row = self::rows()[$id] ?? null;
        return $row ? SiteRoot("shop/c/{$row['slug']}") : SiteRoot('shop');
    }

    // Адрес из названия; если такой уже есть - с номером: futbolki, futbolki-2
    public static function makeSlug(string $text, int $exceptId = 0): string
    {
        $base = TextToUrl(html_entity_decode($text, ENT_QUOTES)) ?: 'category';
        $slug = $base;
        for ($i = 2; ; $i++) {
            $busy = GetDefaultDb()->selectCell("SELECT 1 FROM `shop_categories` WHERE `slug` = ? AND `id` <> ?d", $slug, $exceptId);
            if (!$busy) {
                return $slug;
            }
            $slug = "{$base}-{$i}";
        }
    }

    /**
     * Категория по пути «Одежда / Футболки» (для импорта): недостающие создаются. Возвращает id последней
     */
    public static function findOrCreateByPath(string $path): int
    {
        $parent_id = 0;
        foreach (array_filter(array_map('trim', explode('/', $path)), 'strlen') as $name) {
            $found = null;
            foreach (self::rows() as $row) {
                if ($row['parent_id'] === $parent_id && mb_strtolower(html_entity_decode($row['name'])) === mb_strtolower(html_entity_decode($name))) {
                    $found = $row['id'];
                    break;
                }
            }
            if ($found === null) {
                $category            = new self();
                $category->parent_id = $parent_id;
                $category->name      = $name;
                $category->slug      = self::makeSlug($name);
                $category->is_active = true;
                $found               = (int)$category->flush();
                self::rows(true);
            }
            $parent_id = $found;
        }
        return $parent_id;
    }

    // Сколько активных товаров в каждой категории (с подкатегориями): [id => count]
    public static function productCounts(): array
    {
        static $ret = null;
        if ($ret === null) {
            $direct = array_map('intval', GetDefaultDb()->selectCol(
                "SELECT `category_id` AS ARRAY_KEY, COUNT(*) FROM `shop_products` WHERE `is_active` = 1 AND `category_id` > 0 GROUP BY `category_id`"
            ));
            $ret = [];
            foreach (array_keys(self::rows()) as $id) {
                $ret[$id] = array_sum(array_map(fn($child) => $direct[$child] ?? 0, self::descendantIds($id)));
            }
        }
        return $ret;
    }

    // Подкатегории переходят к родителю, товары - остаются без категории
    public function delete()
    {
        $id        = $this->id;
        $parent_id = $this->parent_id;
        $ret       = parent::delete();
        if ($ret) {
            $this->db->query("UPDATE `shop_categories` SET `parent_id` = ?d WHERE `parent_id` = ?d", $parent_id, $id);
            $this->db->query("UPDATE `shop_products` SET `category_id` = NULL WHERE `category_id` = ?d", $id);
            self::rows(true);
        }
        return $ret;
    }
}
