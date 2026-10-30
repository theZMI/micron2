<div class="container-fluid mb-4">
    <h1 class="mt-4 mb-4">Миграции БД</h1>
    <?php if ($applied): ?>
        <?= MsgOk('Применены: ' . implode(', ', $applied)) ?>
    <?php endif; ?>
    <table class="table table-sm">
        <thead><tr><th>Миграция</th><th>Статус</th></tr></thead>
        <tbody>
            <?php foreach ($all as $name): ?>
                <tr>
                    <td><?= $name ?></td>
                    <td><?= in_array($name, $pending) ? '<span class="badge text-bg-warning">не применена</span>' : '<span class="badge text-bg-success">применена</span>' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($pending): ?>
        <form method="post" action="<?= GetCurUrl() ?>">
            <input type="hidden" name="is_run" value="1">
            <button type="submit" class="btn btn-primary rounded-pill">Применить</button>
        </form>
    <?php endif; ?>
</div>
