<?php

$model  = new DepartmentModel();
$list   = $model->getList();
$action = Get('a');

switch ($action) {
    case 'delete':
        $list[+Get('id')]->delete();
        UrlRedirect::go(GetCurUrl('a='.M_DELETE_PARAM.'&id='.M_DELETE_PARAM));
        break;
}