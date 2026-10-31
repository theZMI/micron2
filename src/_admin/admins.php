<?php

$model  = new AdminModel(intval(Get('id')));
$action = Get('a');

if ($action == 'delete') {
    $model->delete();
}

$list = $model->getList();
