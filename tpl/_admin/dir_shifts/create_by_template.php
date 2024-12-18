<div class="container-fluid mb-4">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">
                <span class="pull-start me-3">Выберите шаблон</span>
            </h1>
            <div class="table-responsive mb-4 table-extra-condensed-wrapper">
                <table class="table table-condensed table-hover table-extra-condensed">
                    <?php if (count($list)): ?>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th colspan="1">Действия</th>
                        </tr>
                        <?php foreach ($list as $id => $v): ?>
                            <tr>
                                <td><?= $v->id ?></td>
                                <td><?= $v->name ?></td>
                                <td width="1%" class="text-center">
                                    <a href="<?= GetCurUrl("step=2&template_id={$v->id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Создать на основе этого шаблона">Далее</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="text-center">Нет данных</td>
                        </tr>
                    <?php endif ?>
                </table>
            </div>
        </div>
    </div>
</div>