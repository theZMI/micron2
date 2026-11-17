<?php

$product = new ShopProductModel(intval(Get('id')) ?: null);
if (!$product->isExists() || !$product->is_active) {
    IncludeCom('404');
    ExitCom();
    return;
}
$boughtTogether = $product->getBoughtTogether(4);
$recentlyViewed = ShopRecentlyViewed::getProducts($product->id, 4);
ShopRecentlyViewed::add($product->id);

BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => $product->name]);
