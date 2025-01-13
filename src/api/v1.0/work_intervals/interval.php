<?php

if (Post('is_set')) {
    $id             = +$id > 0 ? $id : null;
    $model          = new WorkIntervalModel($id);
    $model->user_id = $g_user->id;
    $model->start   = Post('start');
    $model->stop    = Post('stop');
    $model->flush();

    (new ApiResponse())->normal(
        $model->getData()
    );
}

(new ApiResponse())->error('Invalid request');