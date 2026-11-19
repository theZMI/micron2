<?php

$model                 = new UserModel();
$list                  = $model->getList();
$action                = (string)Post('a'); // Удаление - только POST (см. PostActionButton)
$tableHeader           = ['ID', 'ФИО', 'Логин', 'Телефон'];
$tableHeaderEnd        = '<th width="1%" colspan="2">Действия</th>';
$tableHeaderEndFilters = '<td colspan="2"></td>';
$colWidths             = ['ID' => '50', 'ФИО' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $k => $v) {
    $tableData[$v->id] = [
        $v->id,
        $v->full_name,
        $v->login,
        OutputFormats::mobilePhone((string)$v->phone),
    ];
    ob_start();
    ?>
        <td width="1%" class="text-center" style="display: none;">
            <a href="<?= SiteRoot("_admin/users/add_or_edit&id={$v->id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
        </td>
        <td width="1%" class="text-center">
            <?= PostActionButton('delete', ['id' => $v->id], 'Удалить?', 'btn btn-sm btn-danger rounded-pill') ?>
        </td>
    <?php
    $tableDataEnd[$v->id] = ob_get_clean();
}
$defaultTableValues = [];
$tableFilters = [
    'ФИО'    => 'input',
];

switch ($action) {
    case 'delete':
        if (isset($list[intval(Post('id'))])) {
            $list[intval(Post('id'))]->revokeAllSessions();
            $list[intval(Post('id'))]->delete();
        }
        UrlRedirect::go(GetCurUrl('a='.M_DELETE_PARAM.'&id='.M_DELETE_PARAM));
        break;
}