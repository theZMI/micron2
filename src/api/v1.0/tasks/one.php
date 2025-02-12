<?php

ini_set('memory_limit', '512M');

$model         = new TaskModel(+$id);
$canEditFields = [
    'user_comment',
    'photo_1',
    'status'
];

if (Post('is_set')) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, $canEditFields)) {
            continue;
        }
        if ($k === 'photo_1') {
            if (!$v) {
                continue;
            }
            try {
                $model->$k = SaveImageFromBase64(strval($v), "/upl/task_photos/" . +$id);
            } catch (\Throwable $e) {
            }
            continue;
        }
        $model->$k = $v;
    }
}

(new ApiResponse())->normal($model->getData());