<?php

function ModelParam(&$model, $param, $default = '')
{
    return $model->isExists() ? $model->$param : $default;
}