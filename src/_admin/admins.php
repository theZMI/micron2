<?php

$model  = new AdminModel(+Get('id'));
$action = Get('a');

if ($action == 'delete') {
    $model->delete();
}

$list = $model->getList();
