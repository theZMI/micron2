<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start"><?= "Отчёт по смене №".$modelParam('id') ?></span>
            </h1>
            <div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <?php $isFirst = true; foreach ($model->users as $user): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link<?= $isFirst ? ' active' : '' ?>" data-bs-toggle="tab" data-bs-target="#worker-tasks-tab-<?= $user->id ?>-pane" type="button" role="tab" aria-controls="worker-tasks-tab-<?= $user->id ?>-pane" aria-selected="false">
                                <?= $user->full_name ?>
                            </button>
                        </li>
                    <?php $isFirst = false; endforeach; ?>
                </ul>
                <div class="tab-content tab-bordered" id="myTabContent">
                    <?php $isFirst = true; foreach ($model->users as $user): ?>

                        <div id="worker-tasks-tab-<?= $user->id ?>-pane" class="tab-pane fade<?= $isFirst ? ' show active' : '' ?>" role="tabpanel" aria-labelledby="worker-tasks-tab-<?= $user->id ?>-tab" tabindex="0">
                            <?php $shift = (new ShiftModel())->findOne(['dir_id' => $dir_id, 'user_id' => $user->id]) ?>
                            <div class="mb-3">
                                <h4>Задачи:</h4>
                                <?php IncludeCom('_admin/dir_shifts/report_tasks', ['shift' => $shift]) ?>
                                <?php /*
                                <?php if (empty($shift) || !count($shift->tasks)): ?>
                                    <div class="text-center pt-3 pb-3">
                                        Нет задач
                                    </div>
                                <?php else: ?>
                                    <div class="tasks-block">
                                        <?php foreach($shift->tasks as $task): ?>
                                            <?php IncludeCom('_admin/dir_shifts/task', ['task' => $task]) ?>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                                */ ?>
                            </div>

                            <div class="mb-3">
                                <h4>Параметры смены:</h4>
                                <div class="tasks-block">
                                    <?php foreach ($shift->params as $param) {
                                        echo '<div class="tasks-block-task">';
                                        IncludeCom('_admin/params/shift_param', ['model' => $param]);
                                        echo '</div>';
                                    } ?>
                                </div>
                            </div>
                        </div>
                    <?php $isFirst = false; endforeach; ?>
                </div>
            </div>
            <br>
            <div class="text-center">
                <a href="<?= SiteRoot('_admin/reports') ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К списку</a>
            </div>
        </div>
    </div>
</div>