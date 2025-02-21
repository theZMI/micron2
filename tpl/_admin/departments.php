<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Отделы</span>
                <a href="<?= SiteRoot('_admin/departments/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
            </h1>
            <div class="table-responsive table-extra-condensed-wrapper">
                <table class="site-table">
                    <?php if (count($list)): ?>
                        <tr>
                            <th width="50">ID</th>
                            <th>Название</th>
                            <th>Сотрудники</th>
                            <th width="1%" class="text-nowrap">С таймером?</th>
                            <th colspan="1">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)"><?= $v->id ?></td>
                                <td onclick="trClick(this)" class="text-nowrap"><?= $v->name ?></td>
                                <td>
                                    <?php if (count($v->users)): ?>
                                        <ol class="mb-0 ps-0 site-ol-list">
                                        <?php foreach ($v->users as $user): ?>
                                            <li><?= $user->full_name ?></li>
                                        <?php endforeach; ?>
                                        </ol>
                                    <?php else: ?>
                                        <div class="text-secondary">Нет сотрудников</div>
                                    <?php endif ?>
                                </td>
                                <td onclick="trClick(this)" class="text-center"><?= $v->use_timer ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-x-lg"></i>' ?></td>
                                <td width="1%" class="text-center" style="display: none;">
                                    <a href="<?= SiteRoot("_admin/departments/add_or_edit&id={$v->id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
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