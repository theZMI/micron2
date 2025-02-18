<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Отчёты</span>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="table table-condensed table-extra-condensed">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th width="1%">Прогресс по задачам</th>
                            <th colspan="2" style="display: none;">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)"><?= $v->id ?></td>
                                <td onclick="trClick(this)"><?= $v->name ?></td>
                                <td>
                                    <div class="d-flex justify-content-center align-items-center">
                                        <div class="progress" style="width: 300px;">
                                            <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?= intval($v->progress['percent']) ?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                                <?= intval($v->progress['done']) ?> / <?= intval($v->progress['total']) ?>
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
                                                    <div class="progress" style="width: 125px;">
                                                        <div class="progress-bar progress-bar-striped bg-secondary" role="progressbar" style="width: <?= intval($shift->progress['percent']) ?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100">
                                                            <?= intval($shift->progress['done']) ?> / <?= intval($shift->progress['total']) ?>
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
                                    <a href="<?= SiteRoot("_admin/dir_shifts/close&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill text-nowrap" title="Закрыть смену" onclick="return confirm('Вы уверены что хотите закрыть смену?')">Закрыть смену</a>
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