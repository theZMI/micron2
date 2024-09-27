<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4"><?= $isLocked ? 'Открыть сайт' : 'Закрыть сайт' ?></h1>
            <div class="text-start">
                <?php if ($isLocked): ?>
                    <a href="<?= GetCurUrl('a=open') ?>" class="btn btn-primary btn-lg rounded-pill">
                        <i class="bi bi-unlock-fill me-2"></i>
                        Открыть
                    </a>
                <?php else: ?>
                    <a href="<?= GetCurUrl('a=close') ?>" class="btn btn-primary btn-lg rounded-pill">
                        <i class="bi bi-lock-fill me-2"></i>
                        Закрыть
                    </a>
                <?php endif ?>
            </div>
        </div>
    </div>
</div>