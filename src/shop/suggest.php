<?php

// Подсказки для строки поиска в шапке (JSON): shop/suggest?q=...
$q      = trim((string)Get('q', ''));
$result = mb_strlen($q) >= 2 ? (new ShopProductModel())->suggest($q, 6) : ['items' => [], 'total' => 0, 'search_query' => $q];

// Категории, в названии которых есть слова запроса
$stems      = ShopSearch::queryStems($result['search_query']);
$categories = [];
foreach ($stems ? ShopCategoryModel::activeRows() : [] as $row) {
    $words = ShopSearch::words($row['name']);
    $isAll = !array_filter($stems, fn($stem) => !array_filter($words, fn($w) => str_starts_with($w, $stem)));
    if ($isAll) {
        $categories[] = ['name' => html_entity_decode($row['name']), 'url' => SiteRoot("shop/c/{$row['slug']}")];
    }
}

(new ApiResponse())->normal([
    'query'      => html_entity_decode($result['search_query']),
    'total'      => $result['total'],
    'all_url'    => SiteRoot('shop&q=' . urlencode(html_entity_decode($q))),
    'categories' => array_slice($categories, 0, 3),
    'items'      => array_map(fn(ShopProductModel $p) => [
        'name'  => html_entity_decode($p->name),
        'url'   => $p->url(),
        'price' => strip_tags(ShopPrice($p->price)) . $p->priceUnitSuffix(),
        'image' => $p->mainImage(),
    ], $result['items']),
]);
