<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 1000px;">
            <h1 class="my-4">
                <span class="pull-start me-3">Группы доп. услуг</span>
                <a href="<?= SiteRoot('_admin/shop/addon_groups/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end"><i class="bi bi-plus-lg me-2"></i>Добавить</a>
            </h1>
            <p class="text-secondary small">Группа - набор <a href="<?= SiteRoot('_admin/shop/addons') ?>">доп. услуг</a> для товара. У товара может быть несколько групп
                (в карточке товара или колонкой «Доп. услуги» в таблице импорта: названия через «;»), повторяющиеся доп. услуги показываются один раз.</p>
            <?php if (empty($list)): ?>
                <p class="text-muted">Групп пока нет</p>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead><tr><th>Название</th><th>Доп. услуги</th><th class="text-end">Товаров</th><th class="text-end">Порядок</th><th width="1%"></th></tr></thead>
                    <tbody>
                    <?php foreach ($list as $group): ?>
                        <tr>
                            <td><a href="<?= SiteRoot("_admin/shop/addon_groups/add_or_edit&id={$group->id}") ?>"><?= $group->name ?></a></td>
                            <td class="small"><?= implode(', ', array_filter(array_map(fn($id) => $names[$id] ?? null, $group->addonIds()))) ?: '—' ?></td>
                            <td class="text-end"><?= $productCounts[(int)$group->id] ?? 0 ?></td>
                            <td class="text-end"><?= $group->sort ?></td>
                            <td class="text-nowrap">
                                <a href="<?= SiteRoot("_admin/shop/addon_groups/add_or_edit&id={$group->id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
                                <?= PostActionButton('delete', ['id' => $group->id], 'Удалить группу? У товаров она тоже уберётся', 'btn btn-sm btn-danger rounded-pill') ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>
