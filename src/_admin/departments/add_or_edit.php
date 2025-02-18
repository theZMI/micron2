<?php

$model      = new DepartmentModel(+Get('id'));
$modelParam = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $department = Post('department');
    $use_timer  = Post('use_timer') === 'on';
    $errs       = [];

    if (empty($department)) {
        $errs[] = "Введите название отдела";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->department = $department;
        $model->use_timer  = intval($use_timer);

        $id = $model->flush();

        UrlRedirect::go(
            SiteRoot("_admin/departments&sel_id={$id}")
        );
    }
}