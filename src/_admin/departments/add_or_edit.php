<?php

$model      = new DepartmentModel(+Get('id'));
$modelParam = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $department = Post('department');
    $errs       = [];

    if (empty($department)) {
        $errs[] = "Введите название отдела";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->department = $department;

        $id = $model->flush();

        UrlRedirect::go(
            SiteRoot("_admin/departments&sel_id={$id}")
        );
    }
}