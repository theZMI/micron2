<?php

$model->int_data_1    = mt_rand(1, 1000);
$model->string_data_1 = md5(mt_rand(1, 1000));
$model->string_data_2 = "String value = " . md5(mt_rand(1, 1000));
$model->bool_data_1   = mt_rand(0, 1);
$model->float_data_1  = 1 / mt_rand(1, 10);
unset($model);

$toJson = [];
foreach ((new TestSchemeModel())->getList() as $m) {
    $toJson[] = $m->getData();
}

$responser = new ApiResponse();
$responser->normal(
    $toJson
);