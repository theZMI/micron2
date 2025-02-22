<?php

if (!isset($shift)) {
    throw new RuntimeException("Необходимо передать shift");
}

$list                  = $shift->params;
$tableHeader           = ['ID', 'Параметр', 'Статус', 'Значение', 'Установлен в'];
$tableHeaderEnd        = '';
$tableHeaderEndFilters = '';
$colWidths             = ['ID' => '50', 'Параметр' => '33%', 'Значение' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $k => $v) {
    ob_start();
        IncludeCom('_admin/dir_shifts/report_param_value', ['model' => $v]);
    $paramValue = ob_get_clean();

    $tableData[$v->id] = [
        $v->id,
        "<strong class='me-2'>{$v->param->name}</strong>",
        $v->status_label,
        $paramValue,
        OutputFormats::dateTime($v->last_update_time, false),
    ];

    $tableDataEnd[$v->id] = '';
}
$defaultTableValues = [];
$tableFilters = [
    'Параметр' => 'input',
    'Значение' => 'input',
];