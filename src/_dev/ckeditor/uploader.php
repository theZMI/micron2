<?php

$funcNum  = $_GET['CKEditorFuncNum'];
$CKEditor = $_GET['CKEditor'];
$langCode = $_GET['langCode'];
$url      = '';
$message  = '';


if (isset($_FILES) && isset($_FILES['upload']) && isset($_FILES['upload']['tmp_name'])) {
    $err = true;
    if (is_uploaded_file($_FILES['upload']["tmp_name"])) {
        $ext    = pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION);
        $string = md5(uniqid(mt_rand())) . "." . md5($_FILES['upload']['name']) . $ext;
        $subdir = "upl/ckeditor/";
        FileSys::MakeDir(BASEPATH . $subdir);
        $path = BASEPATH . $subdir . $string;
        $link = Root($subdir . $string);
        move_uploaded_file($_FILES['upload']["tmp_name"], $path);

        if (is_file($path)) {
            $url = $link;
            $err = false;
        }
    }
    if ($err) {
        $message = "Can't upload file";
    }
}

echo "<script type='text/javascript'>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '$message');</script>";
?>