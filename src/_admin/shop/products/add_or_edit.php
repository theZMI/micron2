<?php

$model      = new ShopProductModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));

$msg = '';
if (Post('is_set')) {
    $name  = trim((string)Post('name'));
    $price = OutputFormats::fromAmount((string)Post('price', '0'));
    $errs  = [];
    if ($name === '') {
        $errs[] = 'Впишите название товара';
    }
    if ($price <= 0) {
        $errs[] = 'Цена должна быть больше нуля';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name        = $name;
        $model->vendor_code = trim((string)Post('vendor_code'));
        $model->price       = ShopOrderModel::toCents($price) / 100;
        $model->description = (string)Post('description');
        $model->is_active   = (bool)Post('is_active');
        $model->sort        = intval(Post('sort'));
        $id                 = $model->flush();
        UrlRedirect::go(SiteRoot("_admin/shop/products&sel_id={$id}"));
    }
}
