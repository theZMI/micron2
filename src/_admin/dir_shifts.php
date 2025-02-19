<?php

$page        = intval(Get('p', 1));
$action      = Get('a');
$list        = (new DirShiftsModel())->getList();
$tableHeader = [
    'ID',
    'Название',
    'Начать в',
    'Завершить в',
    'Статус'
];
$tableData   = [];
foreach ($list as $k => $v) {
    $tableData[$v->id] = [
        $v->id,
        $v->name,
        OutputFormats::dateTimeRu($v->shifts[0]->start_time, false),
        OutputFormats::dateTimeRu($v->shifts[0]->end_time, false) . ' &nbsp; <span class="badge text-bg-secondary">' . intval($v->shifts[0]->end_time - $v->shifts[0]->start_time + 1) / 86400 . ' д.</span>',
        $v->status_name !== (new ShiftModel())->statuses(ShiftModel::STATUS_CREATED)['name'] ? $v->status_name : '',
    ];
}

switch ($action) {
    case 'delete':
        $list[+Get('id')]->delete();
        UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
        break;
}