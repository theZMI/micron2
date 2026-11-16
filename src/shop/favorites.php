<?php

// Избранное: показ и POST a=toggle&product_id=N (из JS - ответ JSON)
if (Post('a') === 'toggle') {
    $product = new ShopProductModel(intval(Post('product_id')) ?: null);
    $isActive = $product->isExists() && $product->is_active ? ShopFavorites::toggle($product->id) : false;
    if (IsAjaxRequest()) {
        (new ApiResponse())->normal(['is_favorite' => $isActive] + ShopHeaderState());
    }
    UrlRedirect::go(SafeRedirectUrl($_SERVER['HTTP_REFERER'] ?? '', SiteRoot('shop/favorites')));
}

$products = ShopFavorites::getProducts();
BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => 'Избранное']);
