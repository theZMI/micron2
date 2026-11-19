<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h1 class="my-4"><?= $isLocked ? 'Открыть сайт' : 'Закрыть сайт' ?></h1>
            <div class="text-start">
                <?php if ($isLocked): ?>
                    <?= PostActionButton('open', [], '', 'btn btn-primary btn-lg rounded-pill', '<i class="bi bi-unlock-fill me-2"></i> Открыть', 'Открыть сайт') ?>
                <?php else: ?>
                    <?= PostActionButton('close', [], 'Закрыть сайт? Посетители увидят заглушку', 'btn btn-primary btn-lg rounded-pill', '<i class="bi bi-lock-fill me-2"></i> Закрыть', 'Закрыть сайт') ?>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>