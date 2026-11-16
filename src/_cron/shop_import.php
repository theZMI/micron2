<?php

// Импорт товаров из Google-таблицы по расписанию: wget -qO- "https://site.ru/_cron/shop_import?key=CRON_SECRET"
ini_set('max_execution_time', 300);

try {
    $report = (new ShopProductImporter())->import(
        ShopProductImporter::parseCsv(ShopProductImporter::downloadGoogleSheet(Config('shop')['import']['google_sheet_url']))
    );
    echo "Created: {$report['created']}, updated: {$report['updated']}, hidden: {$report['hidden']}<br>";
    foreach ($report['errors'] as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
} catch (Throwable $e) {
    header(Php::status(500));
    echo 'ERROR: ' . htmlspecialchars($e->getMessage()) . '<br>';
}

die('END');
