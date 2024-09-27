<?php

function FillArrayFromPhpInput(&$arr): void
{
    $inputParams = file_get_contents('php://input');
    $arrayParams = json_decode($inputParams, true);
    $arrayParams = empty($arrayParams) ? [] : $arrayParams;
    foreach ($arrayParams as $k => $v) {
        $arr[$k] = $v;
    }
}