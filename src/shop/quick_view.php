<?php

// Содержимое окна «Быстрый просмотр» (загружается из JS в модальное окно)
Config('is_load_in_main_tpl', false);
$product = new ShopProductModel(intval(Get('id')) ?: null);
if (!$product->isExists() || !$product->is_active) {
    header(Php::status(404));
    die('Товар не найден');
}
IncludeCom('shop/_product_details', ['product' => $product, 'full' => false]);
ExitCom();
