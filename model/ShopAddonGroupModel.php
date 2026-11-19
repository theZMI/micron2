<?php

/**
 * Группа доп. товаров/услуг: «Доп. услуги для телефонов», «Доп. услуги на Новый год».
 * У товара может быть несколько групп (в админке или колонкой «Доп. услуги» в таблице) - их доп. услуги объединяются без дублей
 */
class ShopAddonGroupModel extends SiteModel
{
    public function scheme()
    {
        return [
            'id'               => 'int',
            'name'             => 'string',
            'addon_ids'        => 'string', // JSON: id доп. услуг в порядке показа
            'sort'             => 'int',
            'create_time'      => 'int',
            'last_update_time' => 'int',
        ];
    }

    public function createTable()
    {
        return $this->db->query(
            "CREATE TABLE IF NOT EXISTS ?# (
                `id` INT NOT NULL AUTO_INCREMENT,
                `name` VARCHAR(255) NOT NULL DEFAULT '',
                `addon_ids` TEXT DEFAULT NULL,
                `sort` INT NOT NULL DEFAULT 0,
                `create_time` INT DEFAULT NULL,
                `last_update_time` INT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB",
            $this->table
        );
    }

    public function __construct($id = null)
    {
        parent::__construct('shop_addon_groups', $id);
    }

    public function addonIds(): array
    {
        $ids = json_decode((string)$this->addon_ids, true);
        return is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : [];
    }

    public function setAddonIds(array $ids): void
    {
        $this->addon_ids = json_encode(array_values(array_unique(array_filter(array_map('intval', $ids)))));
    }

    public function getAll(): array
    {
        return array_map(fn($id) => new static($id), $this->db->selectCol("SELECT `id` FROM ?# ORDER BY `sort` ASC, `name` ASC", $this->table));
    }

    // Группа по названию (без учёта регистра и пробелов по краям) - для импорта из таблицы
    public static function findByName(string $name): self
    {
        $norm = fn($s) => mb_strtolower(trim(html_entity_decode((string)$s, ENT_QUOTES)));
        foreach ((new self())->getAll() as $group) {
            if ($norm($group->name) === $norm($name)) {
                return $group;
            }
        }
        return new self();
    }

    /**
     * Доп. услуги нескольких групп: в порядке групп, без дублей, только активные. [id => ShopAddonModel]
     */
    public static function addonsOfGroups(array $groupIds): array
    {
        static $cache = []; // Вызывается для каждого товара в корзине и карточке - берём группы одним запросом
        $groupIds = array_values(array_unique(array_filter(array_map('intval', $groupIds))));
        if (!$groupIds) {
            return [];
        }
        $cacheKey = implode(',', $groupIds);
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $rows = GetDefaultDb()->select(
            "SELECT `id`, `addon_ids` FROM `shop_addon_groups` WHERE `id` IN (?a)",
            $groupIds
        );
        $groupIdList = [];
        foreach ($rows as $row) {
            $groupIdList[(int)$row['id']] = json_decode((string)$row['addon_ids'], true);
        }

        $ids = [];
        foreach ($groupIds as $groupId) { // Порядок групп - как у товара, а не как в БД
            if (isset($groupIdList[$groupId]) && is_array($groupIdList[$groupId])) {
                $ids = array_merge($ids, array_map('intval', $groupIdList[$groupId]));
            }
        }
        return $cache[$cacheKey] = ShopAddonModel::activeByIds($ids);
    }

    // При удалении группа убирается у товаров
    public function delete()
    {
        $id  = (int)$this->id;
        $ret = parent::delete();
        if ($ret) {
            foreach ($this->db->select("SELECT `id`, `addon_group_ids` FROM `shop_products` WHERE `addon_group_ids` LIKE ?", '%' . $id . '%') as $row) {
                $ids = array_values(array_diff(json_decode((string)$row['addon_group_ids'], true) ?: [], [$id]));
                $this->db->query("UPDATE `shop_products` SET `addon_group_ids` = ? WHERE `id` = ?d", json_encode($ids), $row['id']);
            }
        }
        return $ret;
    }
}
