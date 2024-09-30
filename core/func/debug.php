<?php

// Print something and show html tags like a text
function Xmp($a)
{
    echo "<xmp>";
    print_r($a);
    echo "</xmp>";
}

// Simple save something into log-file
function ToLog($msg, $path = '')
{
    $path = $path ?: (BASEPATH . 'tmp/from_toLog_function__jj1sdjbxaj1JGFGAixkqP.log');
    FileLogger::Create($path)->Message($msg);
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