<div class="container-fluid">
    <div class="row">
        <div class="col">

            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Администраторы</span>
                <a href="<?= SiteRoot('_admin/admins/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end">
                    <i class="bi bi-plus-lg me-2"></i>Добавить
                </a>
            </h1>
            <div class="table-responsive table-extra-condensed-wrapper mb-4">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Логин</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Описание</th>
                        <th colspan="2">Действия</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $id => $admin): ?>
                        <tr>
                            <td><?= $id ?></td>
                            <td><?= $admin->login ?></td>
                            <td><?= $admin->full_name ?></td>
                            <td><?= $admin->email ?></td>
                            <td><?= $admin->phone ?></td>
                            <td><?= $admin->desc ?></td>
                            <td width="1%" class="text-center">
                                <a href="<?= SiteRoot("_admin/admins/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
                            </td>
                            <td width="1%" class="text-center">
                                <a href="<?= GetCurUrl('a=delete&id=' . $admin->id) ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Удалить данного администратора?')" title="Удалить администратора"><i class="bi bi-trash3-fill"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>