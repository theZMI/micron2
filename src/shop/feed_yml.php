<?php

// Фид товаров в формате YML для Яндекс Товаров / Маркета: /shop/feed_yml (настройки - Config('shop')['seo'])
Config('is_load_in_main_tpl', false);
$config = Config('shop');
$x      = fn($value) => htmlspecialchars(html_entity_decode((string)$value, ENT_QUOTES), ENT_XML1 | ENT_QUOTES);
$money  = fn(int $kopecks) => ShopRubles($kopecks); // Цены у нас в копейках, в фиде - в рублях

$xml   = [];
$xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
$xml[] = '<yml_catalog date="' . date('c') . '">';
$xml[] = '<shop>';
$xml[] = '<name>' . $x($config['seo']['yml_name']) . '</name>';
$xml[] = '<company>' . $x($config['seo']['yml_company']) . '</company>';
$xml[] = '<url>' . $x(SiteRoot()) . '</url>';
$xml[] = '<currencies><currency id="' . ($config['currency'] === 'RUB' ? 'RUR' : $x($config['currency'])) . '" rate="1"/></currencies>';
$xml[] = '<categories>';
foreach (ShopCategoryModel::activeRows() as $row) {
    $xml[] = '<category id="' . $row['id'] . '"' . ($row['parent_id'] ? ' parentId="' . $row['parent_id'] . '"' : '') . '>' . $x($row['name']) . '</category>';
}
$xml[] = '</categories>';
$xml[] = '<offers>';
$activeCategories = ShopCategoryModel::activeRows();
foreach ((new ShopProductModel())->getActiveList() as $product) {
    $xml[] = '<offer id="' . $product->id . '" available="' . ($product->isAvailable() ? 'true' : 'false') . '"' . ($product->variant_group !== '' ? ' group_id="' . abs(crc32($product->variant_group)) . '"' : '') . '>';
    $xml[] = '<name>' . $x($product->name) . '</name>';
    $xml[] = '<url>' . $x($product->url()) . '</url>';
    $xml[] = '<price>' . $money($product->price) . '</price>';
    if ($product->hasDiscount()) {
        $xml[] = '<oldprice>' . $money($product->price_old) . '</oldprice>';
    }
    $xml[] = '<currencyId>' . ($config['currency'] === 'RUB' ? 'RUR' : $x($config['currency'])) . '</currencyId>';
    if (isset($activeCategories[$product->category_id])) {
        $xml[] = '<categoryId>' . $product->category_id . '</categoryId>';
    }
    foreach (array_slice($product->getImages(), 0, 10) as $image) {
        $xml[] = '<picture>' . $x(ShopAbsUrl($image['url'])) . '</picture>';
    }
    if ($product->vendor_code !== '') {
        $xml[] = '<vendorCode>' . $x($product->vendor_code) . '</vendorCode>';
    }
    if ($product->description) {
        $xml[] = '<description>' . $x(mb_strimwidth(trim(strip_tags(html_entity_decode((string)$product->description))), 0, 3000, '…')) . '</description>';
    }
    if (!$product->isPieceUnit()) {
        $xml[] = '<sales_notes>' . $x('Цена за ' . $product->unitName() . ', от ' . ShopQuantity($product->minQuantity(), $product->unitKey())) . '</sales_notes>';
    }
    foreach ($product->getParams() as $name => $value) {
        $xml[] = '<param name="' . $x($name) . '">' . $x($value) . '</param>';
    }
    $xml[] = '</offer>';
}
$xml[] = '</offers>';
$xml[] = '</shop>';
$xml[] = '</yml_catalog>';

header('Content-Type: application/xml; charset=utf-8');
die(implode("\n", $xml));
