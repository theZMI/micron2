<?php

$model = new UserModel();
$page  = intval(Get('page', 1));
$list  = $model->find(['status' => UserModel::STATUS_ACTIVE]);

(new ApiResponse())->normal(
    array_map(fn($v) => $v->getData(), $list)
);