<div class="text-center">
    <span class="glyphicon glyphicon-remove header-icon text-danger"></span>
    <p class="lead text-danger">Ошибка!</p>
    <br>
    <p>
        <?= isset($logger) ? "Информация отправлена разработчикам под номером: <strong>№{$logger->id}</strong>" : "Информация отправлена разработчикам" ?>
    </p>
    <br>
    <a href="<?= SiteRoot() ?>" class="btn btn-link"><span class="glyphicon glyphicon-home"></span> &nbsp; Вернуться на главную</a>
</div>
