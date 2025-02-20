<?php

$model      = new DepartmentModel(+Get('id'));
$modelParam = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

$msg = '';
if (Post('is_set')) {
    $name       = Post('name');
    $use_timer  = Post('use_timer') === 'on';
    $errs       = [];

    if (empty($name)) {
        $errs[] = "Введите название отдела";
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name      = $name;
        $model->use_timer = intval($use_timer);

        $id = $model->flush();

        UrlRedirect::go(
            SiteRoot("_admin/departments&sel_id={$id}")
        );
    }
}