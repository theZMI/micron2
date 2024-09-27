<?php

function IsComIncluded($component)
{
    $files    = get_included_files();
    $paths    = [
        BASEPATH . 'lang/' . DEF_LANG . '/',
        BASEPATH . 'lang/' . LANG . '/',
        BASEPATH . 'src/',
        BASEPATH . 'tpl/',
    ];
    $allNames = [];
    foreach ($files as $file) {
        foreach ($paths as $path) {
            $file = str_replace($path, '', $file);
        }
        $allNames[$file] = $file;
    }
    return in_array($component . '.php', array_keys($allNames));
}

function IsComExists($component)
{
    $paths = [
        BASEPATH . 'lang/' . DEF_LANG . '/',
        BASEPATH . 'lang/' . LANG . '/',
        BASEPATH . 'src/',
        BASEPATH . 'tpl/',
    ];
    foreach ($paths as $path) {
        if (file_exists($path . $component . '.php')) {
            return true;
        }
    }
    return false;
}