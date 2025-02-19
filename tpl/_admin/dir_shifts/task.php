<div class="tasks-block-task">

    <div class="tasks-block-task-task">
        <?= $task->task ?>
        <?php if ($task->deadline_time): ?>
            <div class="tasks-block-task-deadline ps-2 pe-2">До <?= FormatTimeInterval(+$task->deadline_time) ?></div>
        <?php endif; ?>
        <div class="mb-2">
            <?= $task->status_label ?>
        </div>
    </div>
    <?php if (strlen($task->description)): ?>
        <div class="tasks-block-task-info-block mb-2">
            <!--div class="tasks-block-task-info-block-label">Описание задачи:</div-->
            <div class="tasks-block-task-info-block-info tasks-block-task-description">
                <?= nl2br($task->description) ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (strval($task->user_comment) || strval($task->photo_1)): ?>
        <div class="tasks-block-task-separator"></div>
        <div class="tasks-block-task-user-report">
            <img src="<?= Root('i/image/avatars/default_35x35.png') ?>" alt="<?= $task->user->full_name ?>" class="rounded-circle tasks-block-task-user-report-avatar">
            <div class="tasks-block-task-user-report-comment">
                <div class="tasks-block-task-user-report-comment-arrow"><i class="bi bi-caret-left-fill"></i></div>
                <div class="tasks-block-task-user-report-comment-text">
                    <div><?= nl2br(strval($task->user_comment)) ?></div>
                    <?php if (strval($task->photo_1)): ?>
                        <img src="<?= $task->photo_1 ?>" alt="" class="rounded img-fluid">
                    <?php endif; ?>
                    <div class="tasks-block-task-user-report-comment-text-footer">Обновлено: <?= OutputFormats::dateTimeRu(+$task->last_status_change_time) ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>