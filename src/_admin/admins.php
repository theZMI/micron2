<?php

$model  = new AdminModel(intval(Post('id', Get('id'))));
$action = (string)Post('a'); // Удаление - только POST (см. PostActionButton)

if ($action == 'delete') {
    $model->delete();
}

$list = $model->getList();
