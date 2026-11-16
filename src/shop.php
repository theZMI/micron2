<?php

// Каталог: shop, shop&page=2
$perPage  = (int)Config('shop')['catalog_per_page'];
$curPage  = max(1, intval(Get('page', 1)));
$model    = new ShopProductModel();
$products = $model->getActiveList($curPage, $perPage);
$total    = $model->countActive();
BreadCrumb::getInstance()->add(['text' => 'Каталог']);
