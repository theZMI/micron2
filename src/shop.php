<?php

// Каталог: /shop и /shop/c/<категория> (маршрут в core/init/router.php), параметры: ?q=...&price_min=..&price_max=..&f[ключ][]=синий&sort=price_asc&page=2
$category = null;
if (!empty($categorySlug)) {
    $category = ShopCategoryModel::findBySlug($categorySlug);
    if (!$category->isExists() || !isset(ShopCategoryModel::activeRows()[$category->id])) {
        IncludeCom('404');
        ExitCom();
        return;
    }
}

$perPage = (int)Config('shop')['catalog_per_page'];
$sorts   = ShopProductModel::SORTS;
$filter  = [
    'q'           => trim((string)Get('q', '')),
    'category_id' => $category ? (int)$category->id : 0,
    'price_min'   => Get('price_min', ''),
    'price_max'   => Get('price_max', ''),
    'params'      => is_array(Get('f', [])) ? Get('f', []) : [],
    'in_stock'    => (bool)Get('in_stock', false),
    'page'        => max(1, intval(Get('page', 1))),
    'per_page'    => $perPage,
];
if ($filter['q'] !== '') {
    $sorts = [ShopProductModel::SORT_RELEVANCE => 'По релевантности'] + array_diff_key($sorts, ['default' => 1]);
}
$filter['sort'] = array_key_exists((string)Get('sort', ''), $sorts) ? (string)Get('sort') : array_key_first($sorts);

$result      = (new ShopProductModel())->search($filter);
$products    = $result['items'];
$facets      = $result['facets'];
$hasFilters  = $filter['price_min'] !== '' || $filter['price_max'] !== '' || $filter['in_stock'] || array_filter($filter['params']);
$hasStock    = (bool)GetDefaultDb()->selectCell("SELECT 1 FROM `shop_products` WHERE `is_active` = 1 AND `track_stock` = 1 LIMIT 1"); // Фильтр «в наличии» нужен, только если остатки где-то учитываются
$baseUrl     = $category ? $category->url() : SiteRoot('shop');
$isLayoutFix = $filter['q'] !== '' && $result['search_query'] !== $filter['q']; // Нашлось по запросу в другой раскладке

// Категории: подкатегории текущей (или верхний уровень) - плитками над товарами; дерево - в фильтрах
$categoryCounts = ShopCategoryModel::productCounts();
$subcategories  = array_values(array_filter(
    ShopCategoryModel::children($category ? (int)$category->id : 0),
    fn($row) => ($categoryCounts[$row['id']] ?? 0) > 0
));
$categoryPath   = $category ? ShopCategoryModel::path((int)$category->id) : [];

BreadCrumb::getInstance()->add(['text' => 'Каталог', 'link' => $category ? SiteRoot('shop') : '']);
foreach ($categoryPath as $i => $row) {
    BreadCrumb::getInstance()->add(['text' => $row['name'], 'link' => $i < count($categoryPath) - 1 ? SiteRoot("shop/c/{$row['slug']}") : '']);
}

// SEO: заголовок, описание, canonical без фильтров и сортировки; страницы поиска и фильтров не индексируем
$pageTitle            = $category ? $category->name : 'Каталог';
$g_lang['m_title']    = ($filter['q'] !== '' ? "Поиск: {$filter['q']}" : $pageTitle) . ($filter['page'] > 1 ? ", страница {$filter['page']}" : '') . L('m_titlePostfix');
if ($category && $category->description) {
    // Описание категории пришло из Post() уже экранированным: декодируем для длины, но в атрибут выводим снова экранированным
    $g_lang['m_description'] = htmlspecialchars(mb_strimwidth(strip_tags(html_entity_decode($category->description)), 0, 200, '…'), ENT_QUOTES);
}
$canonicalUrl = $baseUrl . ($filter['page'] > 1 ? "?page={$filter['page']}" : '');
$isNoIndex    = $filter['q'] !== '' || $hasFilters || Get('sort', '') !== '';
