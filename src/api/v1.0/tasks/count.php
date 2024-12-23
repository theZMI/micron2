<?php

$model = new TaskModel();
$count = $model->count(TaskModel::STATUS_CREATED);

(new ApiResponse())->normal($count);