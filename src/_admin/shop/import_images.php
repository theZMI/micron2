<?php

// Скачать порцию картинок из таблицы (JSON, вызывает страница импорта в цикле, пока очередь не опустеет)
ini_set('max_execution_time', 120);
$report = ShopProductImporter::syncImages(25, 'админка');
(new ApiResponse())->normal(['report' => $report, 'status' => ShopProductImporter::imagesStatus()]);
