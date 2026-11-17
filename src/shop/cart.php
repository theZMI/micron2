<?php

// Действия с корзиной (POST a=add|set|remove|clear) и её показ
$action = (string)Post('a', '');
if ($action !== '') {
    $product_id = intval(Post('product_id'));
    switch ($action) {
        case 'add':
            $product = new ShopProductModel($product_id ?: null);
            if (!$product->isExists() || !$product->is_active || !$product->isAvailable()) {
                if (IsAjaxRequest()) {
                    (new ApiResponse())->error(['message' => 'Товара нет в наличии'] + ShopHeaderState());
                }
                break;
            }
            ShopCart::add($product_id, max(1, intval(Post('quantity', 1))));
            break;
        case 'set':
            foreach ((array)Post('quantity', []) as $id => $quantity) {
                ShopCart::set((int)$id, (int)$quantity);
            }
            break;
        case 'remove':
            ShopCart::remove($product_id);
            break;
        case 'clear':
            ShopCart::clear();
            break;
    }
    if (IsAjaxRequest()) {
        (new ApiResponse())->normal(ShopHeaderState());
    }
    UrlRedirect::go(SiteRoot('shop/cart'));
}

$items      = ShopCart::getItems();
$total      = ShopCart::total($items);
$isAdjusted = ShopCart::$isAdjusted;
$suggested  = ShopProductModel::boughtTogether(array_map(fn($i) => $i['product']->id, $items), 4);
BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => 'Корзина']);
