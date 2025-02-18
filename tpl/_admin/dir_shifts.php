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
                        <li><a class="dropdown-item" href="<?= SiteRoot('_admin/dir_shifts/add_or_edit') ?>">Уникальная</a></li>
                    </ul>
                </div>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="site-table">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Начать в</th>
                            <th>Завершить в</th>
                            <th>Статус</th>
                            <th colspan="2">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)"><?= $v->id ?></td>
                                <td onclick="trClick(this)"><?= $v->name ?></td>
                                <td onclick="trClick(this)">
                                    <?= OutputFormats::dateTimeRu($v->shifts[0]->start_time, false) ?>
                                </td>
                                <td onclick="trClick(this)">
                                    <?= OutputFormats::dateTimeRu($v->shifts[0]->end_time, false) ?>
                                    <span class="badge text-bg-secondary"><?= intval($v->shifts[0]->end_time - $v->shifts[0]->start_time + 1) / 86400 ?> д.</span>
                                </td>
                                <td onclick="trClick(this)"><?= $v->status_name !== (new ShiftModel())->statuses(ShiftModel::STATUS_CREATED)['name'] ? $v->status_name : '' ?></td>
                                <td width="1%" class="text-center" style="display: none;">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить данные"><i class="bi bi-pencil-fill"></i></a>
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