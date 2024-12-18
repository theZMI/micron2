<?php

$model = new DirShiftTemplatesModel();
$list  = array_filter($model->getList(), fn($v) => $v->is_template);

if (+Get('step') === 2) {
    $template         = new DirShiftTemplatesModel(+Get('template_id'));
    $newDirShiftModel = new DirShiftsModel();

    // Копируем папку со сменами
    $newDirShiftModel->name = $template->name . " - " . OutputFormats::dateForDatePicker(time());
    $dir_id                 = $newDirShiftModel->flush();

    // Копируем смены (ходим по сменам в шаблоне и создаём такие же смены, задачи и параметры в них)
    foreach ($template->shifts as $shift) {
        $newShiftModel          = new ShiftModel();
        $newShiftModel->user_id = $shift->user_id;
        $newShiftModel->dir_id  = $dir_id;
        $shift_id               = $newShiftModel->flush();

        foreach ($shift->tasks as $task) {
            $newTaskModel                           = new TaskModel();
            $newTaskModel->task                     = $task->task;
            $newTaskModel->description              = $task->description;
            $newTaskModel->is_user_comment_required = $task->is_user_comment_required;
            $newTaskModel->is_photo_1_required      = $task->is_photo_1_required;
            $newTaskModel->is_photo_2_required      = $task->is_photo_2_required;
            $newTaskModel->is_photo_3_required      = $task->is_photo_3_required;
            $newTaskModel->is_photo_4_required      = $task->is_photo_4_required;
            $newTaskModel->is_photo_5_required      = $task->is_photo_5_required;
            $newTaskModel->deadline_time            = $task->deadline_time;
            $newTaskModel->shift_id                 = $shift_id;
        }

        foreach ($shift->params as $param) {
            $newParamModel = new ShiftParamModel();
            $newParamModel->shift_id = $shift_id;
            $newParamModel->param_id = $param->param_id;
        }
    }

    UrlRedirect::go(
        SiteRoot("_admin/dir_shifts/add_or_edit&id={$dir_id}")
    );
}