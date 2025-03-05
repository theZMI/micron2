<div class="container-fluid">
    <div class="row">
        <div class="col">

            <h1 class="my-4"><?= $file ?> <span class="badge bg-secondary text-uppercase"><?= $lang ?></span></h1>
            <form action="<?= GetCurUrl() ?>" method="post" class="form-horizontal">
                <input type="hidden" name="is_edit" value="1"/>
                <?= $msg ?>
                <div class="mb-3">
                    <label class="b3-tooltip" data-original-title="Заголовок окна браузера и текста для закладок. Является одной важнейших подсказок контента для поисковиков." data-toggle="tooltip" data-placement="top">
                        Заголовок
                    </label>
                    <input type='text' name='m_title' value='<?= $curLang["m_title"] ?>' class="form-control" autocomplete='off'>
                </div>
                <div class="mb-3">
                    <label class="b3-tooltip" data-original-title="Описание должно содержать самые важные отрывки текста на странице." data-toggle="tooltip" data-placement="top">
                        Описание
                    </label>
                    <input type='text' name='m_description' value='<?= $curLang["m_description"] ?>' class="form-control" autocomplete='off'>
                </div>
                <div class="mb-3">
                    <label class="b3-tooltip" data-original-title="Ключевые слова указываются через запятую. Они помогают поисковым машинам понять по каким фразам будет лучше показывать данную страницу." data-toggle="tooltip" data-placement="top">
                        Ключевые слова
                    </label>
                    <textarea name="m_keyWords" rows="3" class="form-control"><?= $curLang["m_keyWords"] ?></textarea>
                </div>
                <?php foreach ($curLang as $k => $v):
                    if (in_array($k, $exceptions)) {
                        continue;
                    }
                    ?>
                    <div class="mb-3">
                        <label><?= $k ?></label>
                        <?php
                        if (IsNeedShowEditor($v, $k)) {
                            IncludeCom(
                                '_dev/ckeditor/main',
                                [
                                    'toolbar' => 'Full',
                                    'height'  => 120,
                                    'attrs'   => ['name' => base64_encode($k), 'value' => $v]
                                ]
                            );
                        } else {
                            echo "<input type='text' name='" . base64_encode($k) . "' value='" . str_replace("'", '‘', $v) . "' class='form-control' autocomplete='off'>";
                        }
                        ?>
                    </div>
                <?php endforeach ?>
                <div class="text-end">
                    <a href="<?= SiteRoot('_admin/list/list&lang=' . $lang) ?>" class="btn btn-link btn-lg">
                        <i class="bi bi-arrow-left me-2"></i>К списку
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-check-lg me-2"></i>Сохранить</button>
                </div>
            </form>

        </div>
    </div>
</div>