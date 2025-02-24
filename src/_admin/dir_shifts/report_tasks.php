<?php

if (!isset($shift)) {
    throw new RuntimeException("Необходимо передать shift");
}

$list                  = $shift->tasks;
$tableHeader           = ['ID', 'Задача', 'Статус', 'Комментарий', 'Исполнена в'];
$tableHeaderEnd        = ''; // '<th width="1%" colspan="2">Действия</th>';
$tableHeaderEndFilters = ''; // '<td colspan="2"></td>';
$colWidths             = ['ID' => '50', 'Задача' => '33%', 'Комментарий' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $k => $v) {
    ob_start();
    ?>
        <div><?= nl2br(strval($v->user_comment)) ?></div>
        <?php if (strval($v->photo_1)): ?>
            <img src="<?= $v->photo_1 ?>" alt="" class="rounded img-fluid" style="max-width: 400px;">
        <?php endif; ?>
    <?php
    $userComment = ob_get_clean();

    $tableData[$v->id] = [
        $v->id,
        "<strong class='me-2'>{$v->task}</strong>" .
        ( $v->deadline_time_only ? ('<span class="badge text-white text-bg-secondary">До '.FormatTimeInterval(+$v->deadline_time_only).'</span>') : '' ) . '<br>' .
        "<div class='mt-1'>{$v->description}</div>",
        $v->status_label,
        $userComment,
        OutputFormats::dateTime($v->done_time, false),
    ];

    /*
    ob_start();
    ?>
        <td width="1%" class="text-center" style="display: none;">
            <a href="#" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
        </td>
        <td width="1%" class="text-center text-nowrap">
            <a href="<?= GetCurUrl('a=delete&id=' . $v->id) ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Удалить?')" title="Удалить"><i class="bi bi-trash3-fill"></i></a>
        </td>
    <?php
    $tableDataEnd[$v->id] = ob_get_clean();
    */
    $tableDataEnd[$v->id] = '';
}
$defaultTableValues = [];
$tableFilters = [
    'Задача'      => 'input',
    'Комментарий' => 'input',
];