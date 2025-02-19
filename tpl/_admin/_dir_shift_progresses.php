<div class="d-flex justify-content-center align-items-center">
    <div class="progress-stacked" style="width: 300px;">
        <div class="progress" role="progressbar" style="width: <?= intval($model->progress['percent_done']) ?>%" aria-valuenow="<?= intval($model->progress['percent_done']) ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-striped">
                <?= intval($model->progress['done']) ?> / <?= intval($model->progress['total']) ?>
            </div>
        </div>
        <div class="progress" role="progressbar" style="width: <?= intval($model->progress['percent_failed']) ?>%" aria-valuenow="<?= intval($model->progress['percent_failed']) ?>" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar progress-bar-striped bg-danger">
                <?= intval($model->progress['failed']) ?>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-sm btn-link float-end more-info" title="Подробнее...">
        <i class="bi bi-arrow-down"></i>
    </button>
</div>
<div class="more-info pt-2 pb-2" style="display: none;">
    <table class="table table-borderless border border-0 mb-0">
        <?php foreach ($model->shifts as $shift): ?>
            <tr>
                <td class="border border-0 text-nowrap text-end pe-3"><?= $shift->user->full_name ?></td>
                <td class="border border-0">
                    <div class="progress-stacked" style="width: 125px;">
                        <div class="progress" role="progressbar" style="width: <?= intval($shift->progress['percent_done']) ?>%" aria-valuenow="<?= intval($shift->progress['percent_done']) ?>" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar progress-bar-striped">
                                <?= intval($shift->progress['done']) ?> / <?= intval($shift->progress['total']) ?>
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