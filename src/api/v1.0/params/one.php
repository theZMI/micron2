<?php

$model         = new ShiftParamModel(+$id);
$canEditFields = [
    'value',
    'status'
];

if (Post('is_set')) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, $canEditFields)) {
            continue;
        }
        $model->$k = $v;
    }
}

(new ApiResponse())->normal($model->getDataToApi());