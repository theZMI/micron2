<?php

// Промокоды
if (Post('a') === 'delete') {
    $promo = new ShopPromoCodeModel(intval(Post('id')) ?: null);
    if ($promo->isExists()) {
        $promo->delete(); // В заказах код сохранён текстом, поэтому удалять безопасно
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}
$list = (new ShopPromoCodeModel())->getAll();
