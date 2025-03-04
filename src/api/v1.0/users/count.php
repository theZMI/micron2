<?php

$model = new UserModel();
$count = $model->count([UserModel::STATUS_ACTIVE]);

(new ApiResponse())->normal($count);