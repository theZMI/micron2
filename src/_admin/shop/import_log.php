<?php

// Новые строки журнала скачивания картинок (JSON): ?offset=N (байт, -1 - последние строки); &status=1 - и состояние картинок
$log = ShopProductImporter::readLog(intval(Get('offset', -1)));
if (Get('status')) {
    $log['status'] = ShopProductImporter::imagesStatus();
}
(new ApiResponse())->normal($log);
