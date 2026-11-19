<?php

// Доп. товары/услуги: подарочная упаковка, продлённая гарантия... К товарам подключаются группами (_admin/shop/addon_groups)
if (Post('a') === 'delete') {
    $addon = new ShopAddonModel(intval(Post('id')) ?: null);
    if ($addon->isExists()) {
        $addon->delete(); // Из групп убирается сама; в заказах остаётся копия названия и цены
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}
$list   = (new ShopAddonModel())->getAll();
$groups = (new ShopAddonGroupModel())->getAll();
