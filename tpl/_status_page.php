<div class="status-page text-center">
    <span class="status-page-icon">
        <?= $title ?? '<i class="bi bi-arrow-repeat"></i>' ?>
    </span>
    <p class="mb-5 lead"><?= $message ?? 'Данный раздел в разработке...' ?></p>
    <div class="mb-3">
        <a href="javascript:history.back()" class="btn btn-primary btn-lg btn-min-width rounded-pill" title="Вернуться на предыдущую страницу">
            <i class="me-2 bi bi-arrow-left"></i>Назад
        </a>
    </div>
</div>
