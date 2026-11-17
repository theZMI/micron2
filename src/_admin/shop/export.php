<?php

// Выгрузка каталога в CSV (формат импорта): для старта Google-таблицы или резервной копии
Config('is_load_in_main_tpl', false);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="products-' . date('Y-m-d') . '.csv"');
die(ShopProductImporter::exportCsv());
