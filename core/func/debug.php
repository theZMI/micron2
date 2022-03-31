<?php

// Распечатает строку, массив или объект отображая html теги
function Xmp($a)
{
    printf("<xmp>%s</xmp>", print_r($a, true));
}

function VarDump($var)
{
    if (is_bool($var)) {
        $ret = ($var) ? 'true' : 'false';
    } elseif (is_scalar($var)) {
        $ret = htmlspecialchars($var);
    } elseif (is_null($var)) {
        $ret = 'null';
    } else {
        ob_start();
            var_dump($var);
        $data = ob_get_clean();
        $data = preg_replace('/=>\n\s+/', ' => ', $data);
        $data = htmlspecialchars($data);
        $ret  = "<pre>{$data}</pre>";
    }

    return $ret;
}

function ToLog($msg, $path = '')
{
    (FileLogger::Create(BASEPATH . 'tmp/from_toLog_function.log'))->Message($msg);
}
