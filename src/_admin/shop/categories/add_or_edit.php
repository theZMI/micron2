<?php

$model      = new ShopCategoryModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));

$msg = '';
if (Post('is_set')) {
    $name      = trim((string)Post('name'));
    $slug      = TextToUrl(trim((string)Post('slug')));
    $parent_id = intval(Post('parent_id'));
    $errs      = [];
    if ($name === '') {
        $errs[] = 'Впишите название';
    }
    if ($parent_id && (!isset(ShopCategoryModel::rows()[$parent_id]) || ($model->isExists() && in_array($parent_id, ShopCategoryModel::descendantIds((int)$model->id), true)))) {
        $errs[] = 'Нельзя вложить категорию в саму себя или в свою подкатегорию';
    }
    if ($slug !== '' && GetDefaultDb()->selectCell("SELECT 1 FROM `shop_categories` WHERE `slug` = ? AND `id` <> ?d", $slug, (int)$model->id)) {
        $errs[] = "Адрес «{$slug}» уже занят другой категорией";
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name        = $name;
        $model->slug        = $slug !== '' ? $slug : ShopCategoryModel::makeSlug($name, (int)$model->id);
        $model->parent_id   = $parent_id;
        $model->description = (string)Post('description');
        $model->sort        = intval(Post('sort'));
        $model->is_active   = (bool)Post('is_active');
        $model->flush();
        UrlRedirect::go(SiteRoot('_admin/shop/categories'));
    }
}
