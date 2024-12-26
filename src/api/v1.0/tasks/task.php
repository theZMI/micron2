<?php

$model = new TaskModel(+$id);
$canEditFields = [
    'user_comment',
    'photo_1',
    'status'
];

$savePhoto = function($base64, $fileName) {
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
        $data = substr($base64, strpos($base64, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            throw new \Exception('invalid image type');
        }
        $data = str_replace( ' ', '+', $data);
        $data = base64_decode($data);

        if ($data === false) {
            throw new \Exception('base64_decode failed');
        }
    } else {
        throw new \Exception('did not match data URI with image data');
    }

    FileSys::writeFile(
        BASEPATH . "upl/task_photos/{$fileName}.{$type}",
        $data
    );
    return "/upl/task_photos/{$fileName}.{$type}";
};
ToLog("task: {$id}" . print_r($_POST, true));

if (Post('is_set')) {
    foreach ($_POST as $k => $v) {
        if (!in_array($k, $canEditFields)) {
            continue;
        }
        if ($k === 'photo_1') {
            if (!$v) {
                continue;
            }
            $model->$k = $savePhoto(strval($v), +$id);
            continue;
        }
        $model->$k = $v;
    }
}

(new ApiResponse())->normal($model->getData());