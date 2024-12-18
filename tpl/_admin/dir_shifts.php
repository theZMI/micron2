<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Смены</span>
                <div class="dropdown pull-end d-inline-block">
                    <button class="btn btn-primary rounded-pill dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-plus-lg me-2"></i>Добавить
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?= SiteRoot('_admin/dir_shifts/create_by_template') ?>">На основе шаблона</a></li>
                        <li><a class="dropdown-item" href="<?= SiteRoot('_admin/dir_shifts/add_or_edit') ?>">Уникальный</a></li>
                    </ul>
                </div>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th colspan="3">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td><?= $v->id ?></td>
                                <td><?= $v->name ?></td>
                                <td width="1%" class="text-center">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/report&id={$id}") ?>" class="btn btn-sm btn-secondary rounded-pill" title="Отчёт"><i class="bi bi-ui-checks"></i></a>
                                </td>
                                <td width="1%" class="text-center">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
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