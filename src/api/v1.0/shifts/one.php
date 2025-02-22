<?php

$model         = new ShiftModel(+$id);
$canEditFields = [
    'end_time',
    'status',
];

if (Post('is_set')) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, $canEditFields)) {
            continue;
        }
        $model->$k = $v;
    }
}

$model->flush();
$data = $model->getDataToApi();

(new ApiResponse())->normal($data);