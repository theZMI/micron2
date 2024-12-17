<?php

$model  = new UserModel();
$page   = intval(Get('p', 1));
$list   = $model->getList($page);
$action = Get('a');

switch ($action) {
    case 'delete':
        $list[+Get('id')]->delete();
        UrlRedirect::go(GetCurUrl('a='.M_DELETE_PARAM.'&id='.M_DELETE_PARAM));
        break;
}