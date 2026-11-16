<?php

$product = new ShopProductModel(intval(Get('id')) ?: null);
if (!$product->isExists() || !$product->is_active) {
    IncludeCom('404');
    ExitCom();
    return;
}
BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => $product->name]);
