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

function TextDumpColored($var, $exclude = '', $level = 0)
{
    $type = "";
    if (is_array($var)) {
        $type = "<span style='font-weight: bold'>[" . count($var) . "]</span>";
    } elseif (is_object($var)) {
        $type = "<span style='color: orange; font-weight: bold'>" . print_r($var, true) . "</span>";
    }

    if ($type && is_countable($type)) {
        echo $type . "\n";
        foreach ($var as $k => $v) {
            if ((is_array($v) && $k === "GLOBALS") || ($k === $exclude) || ($exclude && substr($k, 3) === $exclude)) {
                continue;
            }
            echo str_repeat(' ', $level * 3) . htmlspecialchars($k) . " => ";
            TextDumpColored($v, $exclude, $level + 1);
        }
        return;
    }
    $res = match (gettype($var)) {
        'boolean'  => "<span style='color: brown'>" . ($var ? 'true' : 'false') . "</span>",
        'string'   => "<span style='color: green'>\"" . htmlspecialchars($var) . '"</span>',
        'NULL'     => "<span style='color: red'>Null</span>",
        'resource' => 'Resource',
        default    => "<span style='color: blue'>" . htmlspecialchars(is_scalar($var) ? $var : print_r($var, true)) . "</span>",
    };
    echo $res . "\n";
}

function __($var, $exclude = '')
{
    if (is_array($var) || is_object($var)) {
        echo "<pre style='font-size: 11px; line-height: 1.15; background: #EEE; color: #000; padding: 15px; border-radius: 10px;'>\n";
        TextDumpColored($var, $exclude);
        echo "</pre>\n";
        return;
    }
    echo "<code>";
    TextDumpColored($var, $exclude);
    echo "</code>\n";
}