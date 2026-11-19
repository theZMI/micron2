<?php

// Сравнение: показ и POST a=toggle&product_id=N | a=clear (из JS - ответ JSON)
if (Post('a') === 'toggle') {
    $product  = new ShopProductModel(intval(Post('product_id')) ?: null);
    $isActive = $product->isExists() && $product->is_active ? ShopCompare::toggle($product->id) : false;
    if (IsAjaxRequest()) {
        (new ApiResponse())->normal(['is_compare' => $isActive] + ShopHeaderState());
    }
    UrlRedirect::go(SafeRedirectUrl($_SERVER['HTTP_REFERER'] ?? '', SiteRoot('shop/compare')));
}
if (Post('a') === 'clear') {
    ShopCompare::clear();
    UrlRedirect::go(SiteRoot('shop/compare'));
}

$products = ShopCompare::getProducts();
$rows     = ShopCompare::rows($products);
$onlyDiff = (bool)Get('diff', false);
BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => 'Сравнение']);
$g_lang['m_title'] = 'Сравнение товаров' . L('m_titlePostfix');
