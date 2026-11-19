<?php

// Карта сайта для поисковиков: /sitemap.xml (маршрут - core/init/router.php, список страниц - core/config/sitemap.php)
Config('is_load_in_main_tpl', false);
$config = Config('sitemap');
$urls   = array_map(fn($uri) => ['loc' => SiteRoot($uri), 'lastmod' => null], (array)$config['pages']);

if (!empty($config['include_shop'])) {
    foreach (ShopCategoryModel::activeRows() as $row) {
        $urls[] = ['loc' => SiteRoot("shop/c/{$row['slug']}"), 'lastmod' => null];
    }
    new ShopProductModel();
    foreach (GetDefaultDb()->select("SELECT `id`, `name`, `last_update_time` FROM `shop_products` WHERE `is_active` = 1 ORDER BY `id`") as $row) {
        $urls[] = ['loc' => SiteRoot("shop/p/{$row['id']}-" . ShopProductModel::slugFromName((string)$row['name'])), 'lastmod' => (int)$row['last_update_time'] ?: null];
    }
}

header('Content-Type: application/xml; charset=utf-8');
$xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $url) {
    $xml .= '  <url><loc>' . htmlspecialchars($url['loc'], ENT_XML1) . '</loc>' . ($url['lastmod'] ? '<lastmod>' . date('Y-m-d', $url['lastmod']) . '</lastmod>' : '') . "</url>\n";
}
die($xml . '</urlset>');
