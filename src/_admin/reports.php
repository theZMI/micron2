<?php

$list                  = (new DirShiftsModel())->getList();
$tableHeader           = ['ID', 'Название', 'Начать в', 'Завершить в', 'Статус',];
$tableHeaderEnd        = '<th width="1%">Прогресс по задачам</th><th width="1%" colspan="2">Действия</th>';
$tableHeaderEndFilters = '<td></td><td colspan="2"></td>';
$colWidths             = ['ID' => '50', 'Название' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $k => $v) {
    $tableData[$v->id] = [
        $v->id,
        $v->name,
        OutputFormats::dateTime($v->shifts[0]->start_time, false),
        OutputFormats::dateTime($v->shifts[0]->end_time, false) . ' &nbsp; <span class="badge text-white text-bg-secondary">' . intval($v->shifts[0]->end_time - $v->shifts[0]->start_time + 1) / 86400 . ' д.</span>',
        $v->status_label,
    ];
    ob_start();
    ?>
        <td class="text-nowrap ps-2 pe-2">
            <?php IncludeCom('_admin/_dir_shift_progresses', ['model' => $v]) ?>
        </td>
        <td class="text-center text-nowrap" style="display: none;">
            <a href="<?= SiteRoot("_admin/dir_shifts/report&id={$v->id}") ?>" class="btn btn-sm btn-secondary rounded-pill" title="Отчёт"><i class="bi bi-ui-checks"></i></a>
        </td>
        <td class="text-center text-nowrap">
            <a href="<?= SiteRoot("_admin/dir_shifts/close&id={$v->id}") ?>" class="btn btn-sm btn-danger rounded-pill text-nowrap" title="Закрыть смену" onclick="return confirm('Вы уверены что хотите закрыть смену?')">Закрыть смену</a>
        </td>
    <?php
    $tableDataEnd[$v->id] = ob_get_clean();
}
$defaultTableValues = [
    'Статус' => strip_tags( (new ShiftModel())->statuses(ShiftModel::STATUS_CREATED)['name'] ),
];
$tableFilters = [
    'Начать в'    => 'input',
    'Завершить в' => 'input',
];