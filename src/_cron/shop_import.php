<?php

// Импорт товаров из Google-таблицы по расписанию: wget -qO- "https://site.ru/_cron/shop_import?key=CRON_SECRET"
ini_set('max_execution_time', 300);

try {
    $report = (new ShopProductImporter())->import(
        ShopProductImporter::parseCsv(ShopProductImporter::downloadGoogleSheet(Config('shop')['import']['google_sheet_url']))
    );
    echo "Created: {$report['created']}, updated: {$report['updated']}, hidden: {$report['hidden']}<br>";
    $images = ShopProductImporter::syncImages(200, 'крон'); // Картинки - сколько успеем (лимит запроса 300 с); остальные докачает следующий запуск
    echo "Images downloaded: {$images['downloaded']}, products left: {$images['left']}<br>";
    foreach ($images['errors'] as $error) {
        echo 'Image: ' . htmlspecialchars($error) . '<br>';
    }
    foreach ($report['errors'] as $error) {
        echo htmlspecialchars($error) . '<br>';
    }
} catch (Throwable $e) {
    header(Php::status(500));
    echo 'ERROR: ' . htmlspecialchars($e->getMessage()) . '<br>';
}

die('END');
