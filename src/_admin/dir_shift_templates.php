<?php

$model  = new DirShiftsModel();
$page   = intval(Get('p', 1));
$list   = array_filter($model->getList($page), fn($v) => $v->is_template);
$action = Get('a');

switch ($action) {
    case 'delete':
        $list[+Get('id')]->delete();
        UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
        break;
}