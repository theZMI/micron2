<?php

$page              = +Get('p', 1);
$model             = new ArticleModel();
$list              = $model->getList($page);
$preview_image_uri = str_replace(BASEPATH, '', Config(['uploader', 'articles', 'upload_path']));
$action            = Get('a');
$article_id        = +Get('id');
$countArticles     = $model->count();
$sel_id            = +Get('sel_id');

if ($action === 'delete') {
    $model = new ArticleModel($article_id);
    $model->delete();
    UrlRedirect::go(
        GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM)
    );
}