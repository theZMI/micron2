<div>
    <h1 class="pull-left"><?= $isAdd ? 'Новая статья' : $model->name ?></h1>
    <div class="clearfix"></div>
</div>
<br>
<form action="<?= GetCurUrl() ?>" id="main-form" <?= Uploader::FORM_LOAD ?>>
    <input type="hidden" name="is_set" value="1">
    <?= $msg ?>
    <div class="row">
        <div class="col-xs-9">
            <div class="form-group">
                <label>Название</label>
                <input type="text" name="name" class="form-control" value="<?= $isEdit ? $model->name : Post('name') ?>"/>
            </div>
        </div>
        <div class="col-xs-3">
            <div class="form-group">
                <label>Статус</label>
                <select name="status" class="form-control">
                    <option value="<?= intval(ArticleModel::STATUS_SHOW) ?>"<?= $isEdit && +$model->status === ArticleModel::STATUS_SHOW ? ' selected' : '' ?>>Отображать</option>
                    <option value="<?= intval(ArticleModel::STATUS_HIDE) ?>"<?= $isEdit && +$model->status === ArticleModel::STATUS_HIDE ? ' selected' : '' ?>>Скрыть</option>
                </select>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label>Статья</label>
        <?php
        IncludeCom(
            '_dev/ckeditor/main',
            [
                'toolbar' => 'Full',
                'height'  => 320,
                'attrs'   => ['name' => 'text', 'value' => Post('text', $isEdit ? $model->text : '')]
            ]
        );
        ?>
    </div>
    <div class="form-group">
        <label>Теги</label>
        <head>
            <script type="text/javascript">
                $(function () {
                    $('#ms-tags').magicSuggest
                    ({
                        placeholder: 'Выбрать...',
                        allowFreeEntries: true,
                        data: [
                            <?php
                            $i = 0;
                            $params = $model->getAllPossibleTags();
                            foreach ($params as $v):
                            ?>
                            '<?= $v ?>'<?= ++$i !== count($params) ? ',' : '' ?>
                            <?php endforeach?>
                        ],
                        value: [
                            <?php if ($isEdit): ?>
                            <?php
                            $i = 0;
                            $params = $model->tags__asArray;
                            foreach ($params as $v):
                            ?>
                            '<?= $v ?>'<?= ++$i !== count($params) ? ',' : '' ?>
                            <?php endforeach; ?>
                            <?php endif ?>
                        ]
                    });
                });
            </script>
        </head>
        <input type="text" name="tags" class="form-control" id="ms-tags"/>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <div class="form-group">
                <label>Превью-изображение</label><br>
                <input type="hidden" name="image_article_rdy_name" value="<?= $isEdit ? $model->preview_image : '' ?>"/>
                <img src="<?= $isEdit ? Root(join([$preview_image_uri, $model->preview_image])) : Root('i/image/def_img_bg.png') ?>" style="width: 400px; height: 225px;" alt="preview" class="is-image_article-1280x720-preview img-rounded">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group">
                <label>Имя автора</label>
                <input type="text" name="author_name" class="form-control" value="<?= $isEdit ? $model->author_name : Post('author_name') ?>"/>
            </div>
            <div class="form-group">
                <label>Аватар автора</label><br>
                <input type="hidden" name="image_avatar_rdy_name" value="<?= $isEdit ? $model->author_avatar : '' ?>"/>
                <img src="<?= $isEdit ? Root($model->author_info['avatar']) : Root('i/image/def_img_bg.png') ?>" style="width: 200px; height: 200px;" alt="preview" class="is-image_avatar-640x640-preview img-rounded">
            </div>
        </div>
    </div>
</form>
<div class="row">
    <div class="col-xs-6">
        <?= $preview_image_form ?>
    </div>
    <div class="col-xs-6">
        <?= $preview_avatar_form ?>
    </div>
</div>
<br>
<br>
<div class="text-center">
    <button type="button" onclick="$('#main-form').trigger('submit')" class="btn btn-primary btn-lg"><i class="fa fa-check icon-left" aria-hidden="true"></i>Сохранить</button>
</div>
<br>
<br>