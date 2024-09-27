<?php

$lang = Get('lang');
$msg  = '';

if (Post('is_create')) {
    $fileName    = FileSys::FilenameSecurity((string)Post('name'));
    $langContent = '<?php' .
        PHP_EOL .
        '$g_lang["head"] = "Header";' . PHP_EOL .
        '$g_lang["text"] = "<p>Text here...</p>";';
    $tplFile     = BASEPATH . "tpl/{$fileName}.php";
    $tplContent  = '<div class="container">' .
        '    <div class="row">' .
        '        <div class="col">' .
        '            <h1 class="mt-4 mb-3"><?= L("head") ?></h1>' .
        '            <?= L("text") ?>' .
        '        </div>' .
        '    </div>' .
        '</div>';
    $isLangWrite = 0;
    $isTplWrite  = false;

    foreach (array_keys(Config('langs')) as $lang) {
        $langFile = BASEPATH . "lang/{$lang}/{$fileName}.php";
        if (!file_exists($langFile)) {
            $isLangWrite += FileSys::writeFile($langFile, $langContent);
        }
    }
    if (!file_exists($tplFile)) {
        $isTplWrite = FileSys::writeFile($tplFile, $tplContent);
    }

    $msg = ($isLangWrite || $isTplWrite)
        ? MsgOk("Страница создана")
        : MsgErr('Ошибка создания страницы, возможно страница уже существует или у вас нет прав на создание файлов');
}