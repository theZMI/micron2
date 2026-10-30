<?php

// Загрузка картинок из CKEditor (доступна только в админке)
Config('is_load_in_main_tpl', false);

$allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$funcNum     = intval($_GET['CKEditorFuncNum'] ?? 0);
$url         = '';
$message     = '';

if (isset($_FILES['upload']['tmp_name']) && is_uploaded_file($_FILES['upload']['tmp_name'])) {
    $ext     = strtolower(pathinfo((string)$_FILES['upload']['name'], PATHINFO_EXTENSION));
    $isImage = in_array($ext, $allowedExts, true) && @getimagesize($_FILES['upload']['tmp_name']) !== false;

    if ($isImage) {
        $fileName = bin2hex(random_bytes(16)) . ".{$ext}";
        $subdir   = "upl/ckeditor/";
        FileSys::makeDir(BASEPATH . $subdir);
        if (move_uploaded_file($_FILES['upload']['tmp_name'], BASEPATH . $subdir . $fileName)) {
            $url = Root($subdir . $fileName);
        }
    }
    $message = $url ? '' : "Can't upload file (allowed: " . implode(', ', $allowedExts) . ")";
}

echo '<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction('
    . $funcNum . ', ' . json_encode($url) . ', ' . json_encode($message)
    . ');</script>';
die;
