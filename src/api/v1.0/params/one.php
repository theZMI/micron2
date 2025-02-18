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
        // Если в value пришла base64 картинка, то пытаемся её сохранить
        if ($k === 'value' && preg_match('/^data:image\/(\w+);base64,/', $v)) {
            try {
                $model->$k = SaveImageFromBase64(strval($v), "/upl/param_photos/" . +$id);
            } catch (\Throwable $exception) {
            }
            continue;
        }
        // Во всех остальных случаях просто сохраняем поле которое есть
        $model->$k = $v;
    }
}

(new ApiResponse())->normal($model->getDataToApi());