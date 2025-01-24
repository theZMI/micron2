<?php

if (Post('is_set')) {
    $user_id          = +$g_user->id;
    $start            = intval(Post('start'));
    $stop             = intval(Post('stop'));
    $id               = +$id > 0
        ? $id
        : (new WorkIntervalModel())->findOne(['user_id' => $user_id, 'temp_id' => +$id]);

    $model          = new WorkIntervalModel($id);
    $model->user_id = $user_id;
    $model->start   = $start ?: $model->start;
    $model->stop    = $stop ?: $model->stop;
    $model->flush();

    (new ApiResponse())->normal(
        $model->getData()
    );
}

(new ApiResponse())->error('Invalid request');