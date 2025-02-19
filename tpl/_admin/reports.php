<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Отчёты</span>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="site-table">
                    <?php if (count($list)): ?>
                        <tr>
                            <th width="1%">ID</th>
                            <th>Название</th>
                            <th width="1%">Начать в</th>
                            <th width="1%">Завершить в</th>
                            <th width="1%">Статус</th>
                            <th width="1%">Прогресс по задачам</th>
                            <th width="1%" colspan="2">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)" class="text-nowrap ps-2 pe-2"><?= $v->id ?></td>
                                <td onclick="trClick(this)" class="ps-2 pe-2"><?= $v->name ?></td>
                                <td onclick="trClick(this)" class="text-nowrap ps-2 pe-2">
                                    <?= OutputFormats::dateTime($v->shifts[0]->start_time, false) ?>
                                </td>
                                <td onclick="trClick(this)" class="text-nowrap ps-2 pe-2">
                                    <?= OutputFormats::dateTime($v->shifts[0]->end_time, false) ?>
                                    <span class="badge text-white text-bg-secondary"><?= intval($v->shifts[0]->end_time - $v->shifts[0]->start_time + 1) / 86400 ?> д.</span>
                                </td>
                                <td onclick="trClick(this)" class="text-nowrap ps-2 pe-2"><?= $v->status_name ?></td>
                                <td class="text-nowrap ps-2 pe-2">
                                    <?php IncludeCom('_admin/_dir_shift_progresses', ['model' => $v]) ?>
                                </td>
                                <td class="text-center text-nowrap" style="display: none;">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/report&id={$id}") ?>" class="btn btn-sm btn-secondary rounded-pill" title="Отчёт"><i class="bi bi-ui-checks"></i></a>
                                </td>
                                <td class="text-center text-nowrap">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/close&id={$id}") ?>" class="btn btn-sm btn-danger rounded-pill text-nowrap" title="Закрыть смену" onclick="return confirm('Вы уверены что хотите закрыть смену?')">Закрыть смену</a>
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