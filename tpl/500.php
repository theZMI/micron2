<div class="page text-center">
    <div class="text-danger">
        <i class="bi bi-exclamation-circle-fill fs-1"></i>
        <br>
        <p class="lead">Ошибка</p>
    </div>
    <p class="mb-4">
        <?= isset($logger) ? "Информация отправлена разработчикам под номером: <strong>№{$logger->id}</strong>" : "Информация отправлена разработчикам" ?>
    </p>
    <div>
        <a href="<?= SiteRoot() ?>" class="btn btn-primary rounded-pill ps-4 pe-4"><i class="bi bi-house-fill me-2"></i>На главную</a>
    </div>
</div>
