<?php

$msg    = '';
$model  = new ArticleModel(+Get('id'));
$isEdit = $model->isExists();
$isAdd  = !$isEdit;
ob_start();
IncludeCom(
    '_ajax_upload',
    [
        'uplName'       => 'image_article',
        'uplConf'       => Config(['uploader', 'articles']),
        'isImageUpload' => true,
        'defWidth'      => 1280,
        'defHeight'     => 720
    ]
);
$preview_image_form = ob_get_clean();
$preview_image_uri  = str_replace(BASEPATH, '', Config(['uploader', 'articles', 'upload_path']));

ob_start();
IncludeCom(
    '_ajax_upload',
    [
        'uplName'       => 'image_avatar',
        'uplConf'       => Config(['uploader', 'users']),
        'isImageUpload' => true,
        'defWidth'      => 640,
        'defHeight'     => 640
    ]
);
$preview_avatar_form = ob_get_clean();
$preview_avatar_uri  = str_replace(BASEPATH, '', Config(['uploader', 'users', 'upload_path']));

if (Post('is_set')) {
    $errs            = [];
    $name            = Post('name');
    $status          = +Post('status');
    $text            = Post('text', '', M_HTML_FILTER_OFF);
    $tags            = Post('tags', []);
    $article_preview = Post('image_article_rdy_name');
    $author_name     = Post('author_name');
    $author_avatar   = Post('image_avatar_rdy_name');

    if (empty($name)) {
        $errs[] = 'Задайте название статьи';
    }

    if (count($errs)) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name          = $name;
        $model->status        = $status;
        $model->text          = $text;
        $model->tags          = json_encode($tags);
        $model->preview_image = $article_preview;
        $model->author_name   = $author_name;
        $model->author_avatar = $author_avatar;
        if ($status === ArticleModel::STATUS_SHOW) {
            $model->publication_time = time();
        }

        $isSaved = $savedId = $model->flush();
        if ($isSaved) {
            UrlRedirect::go(
                SiteRoot("_admin/articles&sel_id={$savedId}")
            );
        } else {
            $msg = MsgErr('Ошибка сохранения');
        }
    }
}