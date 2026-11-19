<?php

// Действия с корзиной (POST a=add|set|remove|clear|promo|promo_remove) и её показ.
// add: product_id, quantity, addons[] (доп. услуги); set: quantity[ключ строки]; remove: key (ключ строки, см. ShopCart::lineKey)
$action = (string)Post('a', '');
if ($action !== '') {
    $product_id = intval(Post('product_id'));
    $promoError = '';
    switch ($action) {
        case 'add':
            $product = new ShopProductModel($product_id ?: null);
            if (!$product->isExists() || !$product->is_active || !$product->isAvailable()) {
                if (IsAjaxRequest()) {
                    (new ApiResponse())->error(['message' => 'Товара нет в наличии'] + ShopHeaderState());
                }
                break;
            }
            ShopCart::add($product_id, Post('quantity', null), array_map('intval', (array)Post('addons', [])));
            break;
        case 'set':
            foreach ((array)Post('quantity', []) as $key => $quantity) {
                ShopCart::set((string)$key, $quantity);
            }
            break;
        case 'remove':
            ShopCart::remove((string)Post('key', (string)$product_id));
            break;
        case 'clear':
            ShopCart::clear();
            break;
        case 'promo':
            $code  = ShopPromoCodeModel::normalizeCode((string)Post('promo_code', ''));
            $promo = ShopPromoCodeModel::findByCode($code);
            $promoError = $code === '' ? 'Введите промокод' : $promo->checkError(ShopCart::total(ShopCart::getItems()));
            if ($promoError === '') {
                ShopCart::setPromoCode($code);
            }
            break;
        case 'promo_remove':
            ShopCart::setPromoCode('');
            break;
    }
    if (IsAjaxRequest()) {
        (new ApiResponse())->normal(ShopHeaderState());
    }
    // Не подошёл промокод - передаём сам код, а причину считаем заново (текст из адреса не показываем: его можно подделать)
    UrlRedirect::go(SiteRoot('shop/cart' . ($promoError !== '' ? '&promo_code=' . urlencode(html_entity_decode((string)Post('promo_code', ''))) : '')));
}

$items      = ShopCart::getItems();
$totals     = ShopCart::totals($items);
$total      = $totals['items'];
$isAdjusted = ShopCart::$isAdjusted;
$promoError = '';
if (($triedCode = ShopPromoCodeModel::normalizeCode((string)Get('promo_code', ''))) !== '') { // Не подошёл введённый промокод
    $promoError = ShopPromoCodeModel::findByCode($triedCode)->checkError($total);
}
// Промокод в корзине перестал подходить (например, уменьшили сумму) - не убираем, а показываем почему не действует
$promoWarning = ShopCart::promoCode() !== '' && $totals['promo_error'] !== '' ? ShopCart::promoCode() . ': ' . $totals['promo_error'] : '';
$suggested  = ShopProductModel::boughtTogether(array_map(fn($i) => $i['product']->id, $items), 4);
BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => SiteRoot('shop')]);
BreadCrumb::getInstance()->add(['text' => 'Корзина']);
