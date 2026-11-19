<?php

/**
 * Доп. товар/услуга к товару: подарочная упаковка, продлённая гарантия, дополнительный ремешок...
 * Цена - фиксированная или процент от цены товара (дробная часть округляется вверх, см. Config('shop')['addons']['percent_round']).
 * К товару доп. услуги подключаются группами (ShopAddonGroupModel). Картинка - квадрат 200x200
 */
class ShopAddonModel extends SiteModel
{
    const PRICE_FIXED   = 'fixed';
    const PRICE_PERCENT = 'percent';

    const UPLOAD_DIR = 'upl/shop/addons/'; // Загруженные картинки (Config(['uploader', 'shop_addons'])), готовая 200x200 - в подпапке
    const IMAGE_SIZE = 200;

    public function scheme()
    {
        return [
            'id'               => 'int',
            'name'             => 'string',
            'description'      => 'string',
            'image'            => 'string', // Имя файла в UPLOAD_DIR/200x200/
            'price_type'       => 'string',
            'price_value'      => 'int', // В копейках (fixed) или в сотых долях процента (percent: 750 = 7,5%)
            'is_service'       => 'bool', // В чеке - услуга (гарантия, упаковка), иначе - товар (ремешок)
            'is_active'        => 'bool',
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
                `description` TEXT DEFAULT NULL,
                `image` VARCHAR(255) NOT NULL DEFAULT '',
                `price_type` VARCHAR(10) NOT NULL DEFAULT 'fixed',
                `price_value` BIGINT NOT NULL DEFAULT 0,
                `is_service` TINYINT(1) NOT NULL DEFAULT 1,
                `is_active` TINYINT(1) NOT NULL DEFAULT 1,
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
        parent::__construct('shop_addons', $id);
    }

    public static function priceTypes(): array
    {
        return [self::PRICE_FIXED => 'Фиксированная, ' . Config('shop')['currency_sign'], self::PRICE_PERCENT => '% от цены товара'];
    }

    /**
     * Цена доп. услуги в копейках для товара с ценой $productPrice (в копейках, за единицу). Процент округляется вверх до
     * Config('shop')['addons']['percent_round'] (в рублях: 1 - до рубля, 0.01 - до копейки). Всё в целых числах
     */
    public function priceFor(int $productPrice): int
    {
        if ($this->price_type !== self::PRICE_PERCENT) {
            return (int)$this->price_value;
        }
        $step  = max(1, (int)ShopKopecks((string)(Config('shop')['addons']['percent_round'] ?? 1)));
        $exact = $productPrice * (int)$this->price_value; // Цена (коп.) × сотые доли процента; в копейках это / 10000
        return intdiv($exact + 10000 * $step - 1, 10000 * $step) * $step; // Вверх до шага округления
    }

    // «+1 000 ₽» или «+7,5% от цены»
    public function priceText(): string
    {
        return $this->price_type === self::PRICE_PERCENT
            ? '+' . ShopPromoCodeModel::percentText((int)$this->price_value) . '% от цены'
            : '+' . ShopPrice((int)$this->price_value);
    }

    public function imageUrl(): string
    {
        return $this->image !== '' ? Root(self::UPLOAD_DIR . self::IMAGE_SIZE . 'x' . self::IMAGE_SIZE . '/' . $this->image) : '';
    }

    // Картинка из загрузчика (_ajax_upload): берём только имя файла, проверяем, что готовая 200x200 есть
    public static function isUploadedImage(string $name): bool
    {
        $name = basename($name);
        return $name !== '' && is_file(BASEPATH . self::UPLOAD_DIR . self::IMAGE_SIZE . 'x' . self::IMAGE_SIZE . '/' . $name);
    }

    public function getAll(): array
    {
        return array_map(fn($id) => new static($id), $this->db->selectCol("SELECT `id` FROM ?# ORDER BY `sort` ASC, `name` ASC", $this->table));
    }

    // Активные по списку id в том же порядке (ключ - id)
    public static function activeByIds(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (!$ids) {
            return [];
        }
        new self();
        $active = array_flip(array_map('intval', GetDefaultDb()->selectCol("SELECT `id` FROM `shop_addons` WHERE `is_active` = 1 AND `id` IN (?a)", $ids)));
        $ret    = [];
        foreach ($ids as $id) {
            if (isset($active[$id])) {
                $ret[$id] = new self($id);
            }
        }
        return $ret;
    }

    // При удалении доп. услуга убирается из групп
    public function delete()
    {
        $id  = (int)$this->id;
        $ret = parent::delete();
        if ($ret) {
            foreach ((new ShopAddonGroupModel())->getAll() as $group) {
                if (in_array($id, $group->addonIds(), true)) {
                    $group->setAddonIds(array_diff($group->addonIds(), [$id]));
                    $group->flush();
                }
            }
        }
        return $ret;
    }
}
