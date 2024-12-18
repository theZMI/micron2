<?php

$msg        = '';
$model      = new ParamModel(+Get('id'));
$modelParam = function ($param, $default = '') use (&$model) {
    return ModelParam($model, $param, Post($param, $default));
};

if (Post('is_set')) {
    $errs = [];
    $name = Post('name');
    $type = +Post('type');

    if (empty($name)) {
        $errs[] = 'Введите название параметра';
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name = $name;
        $model->type = $type;

        $id = $model->flush();
        UrlRedirect::go(
            SiteRoot("_admin/params&sel_id={$id}")
        );
    }
}