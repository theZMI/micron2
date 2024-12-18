<?php

$model = new DirShiftTemplatesModel();
$list = array_filter($model->getList(), fn($v) => $v->is_template);

if (+Get('step') === 2) {
    $template = new DirShiftTemplatesModel(+Get('template_id'));
}