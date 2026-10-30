<?php

$lang       = Get('lang');
$file       = Get('file');
$msg        = '';

// Редактировать можно только существующие языковые файлы внутри lang/
$langFile = realpath(BASEPATH . "lang/{$lang}/{$file}");
if (!$langFile || !str_starts_with($langFile, realpath(BASEPATH . 'lang') . '/') || !str_ends_with($langFile, '.php')) {
    IncludeCom('404');
    ExitCom();
    return;
}
$exceptions = ['m_title', 'm_description', 'm_keyWords'];

if (Post('is_edit')) {
    $prefix  = "<?php\r\n\r\n";
    $postfix = '';
    $lines   = [];
    $title   = (string)Post('m_title');
    $desc    = (string)Post('m_description');
    $kw      = (string)Post('m_keyWords');

    unset($_POST['is_edit']);
    unset($_POST['m_title']);
    unset($_POST['m_description']);
    unset($_POST['m_keyWords']);

    foreach ($_POST as $k => $v) {
        if (in_array($k, $exceptions)) {
            continue;
        }
        $_POST[base64_decode($k)] = $v;
        unset($_POST[$k]);
    }
    if ($file !== (Config('main_tpl') . '.php')) {
        if (!empty($title)) {
            $lines[] = '$g_lang["m_title"]       = ' . var_export($title, true) . ';';
        }
        if (!empty($desc)) {
            $lines[] = '$g_lang["m_description"] = ' . var_export($desc, true) . ';';
        }
        if (!empty($kw)) {
            $lines[] = '$g_lang["m_keyWords"]    = ' . var_export($kw, true) . ';';
        }
    }
    // var_export экранирует ключи и значения, поэтому из формы нельзя внедрить php-код в языковой файл
    foreach ($_POST as $k => $v) {
        $v       = str_replace("\r", '', (string)$v);
        $lines[] = '$g_lang[' . var_export((string)$k, true) . '] = ' . var_export($v, true) . ';';
    }
    $text = $prefix . implode("\r\n", $lines) . $postfix;

    if (is_readable(BASEPATH . "lang/{$lang}/{$file}")) {
        // Если нужно делать backup-ы изменений то сохраняем их
        if (Config(['admin_list', 'with_backups'])) {
            if (file_exists(BASEPATH . "lang/backup/{$lang}/{$file}")) {
                unlink(BASEPATH . "lang/backup/{$lang}/{$file}");
            }
            $fileTo = BASEPATH . "lang/backup/{$lang}/{$file}__" . time();
            FileSys::makeDir(dirname($fileTo));
            copy(BASEPATH . "lang/{$lang}/{$file}", $fileTo);
        }
        $isWrite = FileSys::writeFile(BASEPATH . "lang/{$lang}/{$file}", $text);
        $msg     = $isWrite ? MsgOk('Файл успешно изменён') : MsgErr('Ошибка изменения файла');
    }
}


// Выводим редактор текущего языкового файла
$oldLang = $g_lang;
$g_lang  = [];
require BASEPATH . "lang/{$lang}/{$file}";
$curLang                  = $g_lang;
$curLang['m_title']       = $curLang['m_title'] ?? '';
$curLang['m_description'] = $curLang['m_description'] ?? '';
$curLang['m_keyWords']    = $curLang['m_keyWords'] ?? '';
$g_lang                   = $oldLang;