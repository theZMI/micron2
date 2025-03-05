<?php

// $tableHeader - массив заголовков таблицы
// $tableHeaderEnd - html контент в конце headers
// $tableHeaderEndFilters - html контент в конце фильтров у header-ов
// $colWidths = ['ID' => '100', 'Название' => 'auto'] - определяет ширину колонок (по умолчанию каждая колонка - это 1% но без переноса)
// $tableData - массив данных таблицы
// $tableDataEnd - массив html контент-ов в конце таблицы (добавленные столбцы у каждой строки, формат [N-строки соответсвующий $tableData[N] => html_строка, ...]
// $defaultTableValues - дефолтный выбор в фильтрах
// $tableFilters - какие типы фильтров у каждого столбца (input, select, auto)

if (!isset($tableHeader)) throw new RuntimeException("tableHeader must be set");
$tableHeaderEnd        = $tableHeaderEnd ?? '';
$tableHeaderEndFilters = $tableHeaderEndFilters ?? '';
$colWidths             = $colWidths ?? [];
if (!isset($tableData)) throw new RuntimeException("tableData must be set");
$tableDataEnd          = $tableDataEnd ?? '';
$defaultTableValues    = $defaultTableValues ?? [];
$tableFilters          = $tableFilters ?? [];
$divID                 = uniqid('');
$smartStripTags        = function ($html, $openComments = false) {
    $html = preg_replace_callback(
        '/<!--.*?-->|<[^>]+>/s',
        function ($matches) {
            return str_starts_with($matches[0], '<!--') ? $matches[0] : '';
        },
        $html
    );
    $html = $openComments ? strtr($html, ['<!--' => '', '-->' => '']) : $html;
    return trim($html);
};