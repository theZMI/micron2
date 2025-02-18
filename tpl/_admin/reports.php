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
                            <th>ID</th>
                            <th>Название</th>
                            <th>Начать в</th>
                            <th>Завершить в</th>
                            <th>Статус</th>
                            <th width="1%">Прогресс по задачам</th>
                            <th colspan="2" style="display: none;">Действия</th>
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
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="progress-stacked" style="width: 300px;">
                                            <div class="progress" role="progressbar" style="width: <?= intval($v->progress['percent_done']) ?>%" aria-valuenow="<?= intval($v->progress['percent_done']) ?>" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-striped">
                                                    <?= intval($v->progress['done']) ?> / <?= intval($v->progress['total']) ?>
                                                </div>
                                            </div>
                                            <div class="progress" role="progressbar" style="width: <?= intval($v->progress['percent_failed']) ?>%" aria-valuenow="<?= intval($v->progress['percent_failed']) ?>" aria-valuemin="0" aria-valuemax="100">
                                                <div class="progress-bar progress-bar-striped bg-danger">
                                                    <?= intval($v->progress['failed']) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-link float-end more-info" title="Подробнее...">
                                            <i class="bi bi-arrow-down"></i>
                                        </button>
                                    </div>
                                    <div class="more-info pt-2 pb-2" style="display: none;">
                                        <table class="table table-borderless border border-0 mb-0">
                                            <?php foreach ($v->shifts as $shift): ?>
                                            <tr>
                                                <td class="border border-0 text-nowrap text-end pe-3"><?= $shift->user->full_name ?></td>
                                                <td class="border border-0">
                                                    <div class="progress-stacked" style="width: 125px;">
                                                        <div class="progress" role="progressbar" style="width: <?= intval($shift->progress['percent_done']) ?>%" aria-valuenow="<?= intval($shift->progress['percent_done']) ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar progress-bar-striped">
                                                                <?= intval($shift->progress['done']) ?> / <?= intval($v->progress['total']) ?>
                                                            </div>
                                                        </div>
                                                        <div class="progress" role="progressbar" style="width: <?= intval($shift->progress['percent_failed']) ?>%" aria-valuenow="<?= intval($shift->progress['percent_failed']) ?>" aria-valuemin="0" aria-valuemax="100">
                                                            <div class="progress-bar progress-bar-striped bg-danger">
                                                                <?= intval($shift->progress['failed']) ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                </td>
                                <td width="1%" class="text-center" style="display: none;">
                                    <a href="<?= SiteRoot("_admin/dir_shifts/report&id={$id}") ?>" class="btn btn-sm btn-secondary rounded-pill" title="Отчёт"><i class="bi bi-ui-checks"></i></a>
                                </td>
                                <td width="1%" class="text-center">
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