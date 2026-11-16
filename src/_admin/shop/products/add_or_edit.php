<?php

$model      = new ShopProductModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));

// Характеристики в форме - по строке на каждую: «Состав: 100% хлопок»
$paramsFromText = function (string $text): array {
    $params = [];
    foreach (preg_split('/\R/', $text) as $line) {
        [$name, $value] = array_map('trim', explode(':', $line, 2)) + [1 => ''];
        if ($name !== '' && $value !== '') {
            $params[$name] = $value;
        }
    }
    return $params;
};
$paramsText = Post('params_text') !== false
    ? (string)Post('params_text')
    : implode("\n", array_map(fn($k, $v) => "{$k}: {$v}", array_keys($model->getParams()), $model->getParams()));

$msg = '';
if (Post('is_set')) {
    $name  = trim((string)Post('name'));
    $price    = OutputFormats::fromAmount((string)Post('price', '0'));
    $priceOld = OutputFormats::fromAmount((string)Post('price_old', '0'));
    $errs     = [];
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
        $model->price_old   = ShopOrderModel::toCents(max(0, $priceOld)) / 100;
        $model->description = (string)Post('description');
        $model->is_active   = (bool)Post('is_active');
        $model->sort        = intval(Post('sort'));
        $model->setParams($paramsFromText((string)Post('params_text', '')));
        $id                 = $model->flush();
        UrlRedirect::go(SiteRoot("_admin/shop/products&sel_id={$id}"));
    }
}
