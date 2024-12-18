<?php

$page     = intval(Get('p', 1));
$action   = Get('a');
$list     = (new ParamModel())->getList();

switch ($action) {
    case 'delete':
        $model = new ParamModel(+Get('id'));
        $model->delete();
        UrlRedirect::go(GetCurUrl('a='.M_DELETE_PARAM.'&id='.M_DELETE_PARAM));
        break;
}