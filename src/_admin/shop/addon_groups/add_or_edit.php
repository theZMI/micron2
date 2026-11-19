<?php

$model      = new ShopAddonGroupModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));
$addons     = (new ShopAddonModel())->getAll();
$selected   = Post('is_set') ? array_map('intval', (array)Post('addon_ids', [])) : $model->addonIds();

$msg = '';
if (Post('is_set')) {
    $name = trim((string)Post('name'));
    $errs = [];
    if ($name === '') {
        $errs[] = 'Впишите название';
    } elseif (($same = ShopAddonGroupModel::findByName($name))->isExists() && (int)$same->id !== (int)$model->id) {
        $errs[] = 'Группа с таким названием уже есть - по названию группы подключаются из таблицы импорта';
    }
    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name = $name;
        $model->sort = intval(Post('sort'));
        // Порядок - как в списке доп. услуг (их поле «Порядок»)
        $model->setAddonIds(array_values(array_filter(array_map(fn($a) => (int)$a->id, $addons), fn($id) => in_array($id, $selected, true))));
        $model->flush();
        UrlRedirect::go(SiteRoot('_admin/shop/addon_groups'));
    }
}
