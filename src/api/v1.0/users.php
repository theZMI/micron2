<?php

$model = new UserModel();
$page  = intval(Get('page', 1));
$list  = $model->getList($page);

(new ApiResponse())->normal(
    array_map(fn($v) => $v->getData(), $list)
);