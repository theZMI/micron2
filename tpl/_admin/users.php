<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Сотрудники</span>
                <a href="<?= SiteRoot('_admin/users/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
            </h1>
            <div class="table-responsive table-extra-condensed-wrapper mb-4">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Отдел</th>
                            <th>Роль</th>
                            <th>Логин</th>
                            <th>Телефон</th>
                            <th colspan="2">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td><?= $v->id ?></td>
                                <td>
                                    <?= $v->full_name ?>
                                </td>
                                <td><?= $v->department?->department ?></td>
                                <td><?= $v->roles($v->role) ?: '' ?></td>
                                <td><?= $v->login ?></td>
                                <td><?= OutputFormats::mobilePhone((string)$v->phone) ?></td>
                                <td width="1%" class="text-center">
                                    <a href="<?= SiteRoot("_admin/users/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
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