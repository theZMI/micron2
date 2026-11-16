<?php

// Каталог: shop?q=...&price_min=..&price_max=..&f[Цвет][]=синий&sort=price_asc&page=2
$perPage = (int)Config('shop')['catalog_per_page'];
$filter  = [
    'q'         => trim((string)Get('q', '')),
    'price_min' => Get('price_min', ''),
    'price_max' => Get('price_max', ''),
    'params'    => is_array(Get('f', [])) ? Get('f', []) : [],
    'sort'      => array_key_exists((string)Get('sort', ''), ShopProductModel::SORTS) ? (string)Get('sort') : 'default',
    'page'      => max(1, intval(Get('page', 1))),
    'per_page'  => $perPage,
];
$result      = (new ShopProductModel())->search($filter);
$products    = $result['items'];
$facets      = $result['facets'];
$hasFilters  = $filter['price_min'] !== '' || $filter['price_max'] !== '' || array_filter($filter['params']);

BreadCrumb::getInstance()->add(['text' => 'Каталог']);
