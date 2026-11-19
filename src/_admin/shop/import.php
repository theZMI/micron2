<?php

// Импорт товаров: из Google-таблицы (ссылка в .env: SHOP_IMPORT_GOOGLE_SHEET_URL) или из загруженного CSV-файла
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M'); // Импорт держит в памяти всю таблицу и все строки CSV: при 32M из php_ini.php он падал с OOM

$sheetUrl = Config('shop')['import']['google_sheet_url'];
$report   = null;
$msg      = '';

// Картинки качаются после импорта порциями: страница вызывает _admin/shop/import_images, пока очередь не опустеет,
// и показывает журнал (_admin/shop/import_log). Без JS - кнопкой, по порции за раз
$action = (string)Post('a', '');
if ($action === 'images') {
    ShopProductImporter::syncImages(25, 'админка');
    UrlRedirect::go(GetCurUrl());
} elseif ($action !== '') {
    try {
        if ($action === 'google') {
            if ($sheetUrl === '') {
                throw new RuntimeException('Не задана ссылка на таблицу: SHOP_IMPORT_GOOGLE_SHEET_URL в .env');
            }
            $csv = ShopProductImporter::downloadGoogleSheet($sheetUrl);
        } elseif ($action === 'file') {
            $file = $_FILES['csv'] ?? null;
            if (!$file || $file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
                throw new RuntimeException('Выберите CSV-файл');
            }
            $csv = (string)file_get_contents($file['tmp_name']);
        } else {
            throw new RuntimeException('Неизвестное действие');
        }
        $report = (new ShopProductImporter())->import(ShopProductImporter::parseCsv($csv));
    } catch (Throwable $e) {
        $msg = MsgErr('Импорт остановлен, каталог не изменён. ' . htmlspecialchars($e->getMessage()));
    }
}
$imagesStatus = ShopProductImporter::imagesStatus();
$imagesLog    = ShopProductImporter::readLog(-1, 200);
$autoStart    = $report !== null && $imagesStatus['pending'] > 0; // Сразу после импорта - начинаем качать
