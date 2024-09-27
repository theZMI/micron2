<div>
    <h1 class="pull-left">Статьи блога</h1>
    <a href="<?= SiteRoot('_admin/articles/add_or_edit') ?>" class="btn btn-primary btn-lg pull-left ms-15 mt-30">
        <span class="fa fa-plus icon-left"></span>Добавить
    </a>
    <div class="clearfix"></div>
</div>
<br>
<div class="table-responsive">
    <table class="table table-condensed table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>Статья</th>
            <th>Превью</th>
            <th>Автор</th>
            <th colspan="2">Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($list)): ?>
            <tr>
                <td colspan="99" class="text-center">
                    Нет данных
                </td>
            </tr>
        <?php else: ?>
            <?php foreach ($list as $id => $v): ?>
                <tr class="<?= +$sel_id === +$id ? 'warning' : '' ?>">
                    <td>
                        <?= $id ?><br>
                        <?= +$v->status === ArticleModel::STATUS_SHOW ? '<span class="label label-success">Опубликовано</span>' : '' ?>
                    </td>
                    <td>
                        <strong><?= $v->name ?></strong><br>
                        <p class="text-muted"><?= $v->desc ?></p>
                    </td>
                    <td>
                        <img src="<?= Root("{$preview_image_uri}{$v->preview_image}") ?>" alt="" style="width: 64px;" class="img-rounded"/>
                    </td>
                    <td>
                        <img src="<?= Root($v->author_info['avatar']) ?>" alt="" style="width: 64px;" class="img-circle"/><br>
                        <?= $v->author_info['name'] ?>
                    </td>
                    <td width="1%" class="text-center">
                        <a href="<?= SiteRoot("_admin/articles/add_or_edit&id=" . $id) ?>" class="btn btn-sm btn-primary" title="Изменить статью"><span class="fa fa-edit"></span></a>
                    </td>
                    <td width="1%" class="text-center">
                        <a href="<?= GetCurUrl('a=delete&id=' . $v->id) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить статью?')" title="Удалить статью"><span class="fa fa-trash-o"></span></a>
                    </td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
        </tbody>
    </table>
</div>
<div class="text-center">
    <?php
    IncludeCom(
        "_dev/paginator",
        [
            "pageUrl"      => GetCurUrl("p=" . M_PAGINATOR_PAGE),
            "firstPageUrl" => GetCurUrl("p=" . M_DELETE_PARAM),
            "total"        => $countArticles,
            "perPage"      => ArticleModel::PAGE_LIMIT,
            "curPage"      => Get("p", 1)
        ]
    );
    ?>
</div>