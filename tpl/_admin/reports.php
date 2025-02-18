<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Отчёты</span>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th width="220">Прогресс по задачам</th>
                            <th colspan="2" style="display: none;">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td onclick="trClick(this)"><?= $v->id ?></td>
                                <td onclick="trClick(this)"><?= $v->name ?></td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped" role="progressbar" style="width: <?= intval($v->progress) ?>%" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100"></div>
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