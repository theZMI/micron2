<?php

// Группы доп. услуг: «Доп. услуги для телефонов», «Доп. услуги на Новый год». Группа подключается к товару в его карточке или колонкой «Доп. услуги» в таблице
if (Post('a') === 'delete') {
    $group = new ShopAddonGroupModel(intval(Post('id')) ?: null);
    if ($group->isExists()) {
        $group->delete(); // У товаров группа уберётся сама
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}
$list   = (new ShopAddonGroupModel())->getAll();
$addons = (new ShopAddonModel())->getAll();
$names  = array_column(array_map(fn($a) => ['id' => (int)$a->id, 'name' => $a->name], $addons), 'name', 'id');
// Сколько товаров в каждой группе
$productCounts = [];
new ShopProductModel();
foreach (GetDefaultDb()->selectCol("SELECT `addon_group_ids` FROM `shop_products` WHERE `addon_group_ids` IS NOT NULL AND `addon_group_ids` NOT IN ('', '[]')") as $json) {
    foreach (json_decode((string)$json, true) ?: [] as $id) {
        $productCounts[(int)$id] = ($productCounts[(int)$id] ?? 0) + 1;
    }
}
