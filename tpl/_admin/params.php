<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Словарь параметров смен</span>
                <a href="<?= SiteRoot('_admin/params/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
            </h1>
            <div class="table-responsive table-extra-condensed-wrapper">
                <table class="site-table">
                    <?php if (count($list)): ?>
                        <tr>
                            <th width="50">ID</th>
                            <th>Название</th>
                            <th>Тип</th>
                            <th colspan="1">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)"><?= $v->id ?></td>
                                <td onclick="trClick(this)"><?= $v->name ?></td>
                                <td onclick="trClick(this)"><?= $v->type_name ?></td>
                                <td width="1%" class="text-center" style="display: none">
                                    <a href="<?= SiteRoot("_admin/params/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
                                </td>
                                <td width="1%" class="text-center">
                                    <a href="<?= GetCurUrl('a=delete&id=' . $v->id) ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Удалить?')" title="Удалить"><i class="bi bi-trash3-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="text-center">Нет данных</td>
                        </tr>
                    <?php endif ?>
                </table>
            </div>
        </div>
    </div>
</div>