<h1 class="mt-4 mb-4">Проверка отправки email-ов</h1>
<form action="<?= GetCurUrl() ?>" method="post">
    <input type="hidden" name="is_set" value="1" />
    <?= $msg ?>
    <button type="submit" class="btn btn-primary">Отправить тестовое письмо</button>
</form>