<?php

static $editor = null;

if (is_null($editor)) {
    require_once BASEPATH . 'lib/CKEditor/ckeditor.php';

    $toolbar = $toolbar ?? 'Full';
    $height  = $height ?? 200;

    $editor = new CKEditor(Root('lib/CKEditor/'));

    $editor->config['filebrowserUploadUrl'] = SiteRoot('_dev/ckeditor/uploader');
    $editor->config['toolbar']              = $toolbar;
    $editor->config['height']               = $height;
}

$editor->editor($attrs['name'], $attrs['value']);