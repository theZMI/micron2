<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 1100px;">
            <h1 class="my-4">
                <span class="pull-start me-3">Доп. товары и услуги</span>
                <a href="<?= SiteRoot('_admin/shop/addons/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end"><i class="bi bi-plus-lg me-2"></i>Добавить</a>
            </h1>
            <p class="text-secondary small">Подарочная упаковка, продлённая гарантия, дополнительный ремешок... На карточке товара покупатель выбирает их кликом, цена сразу пересчитывается.
                К товару они подключаются <a href="<?= SiteRoot('_admin/shop/addon_groups') ?>">группами</a>: в карточке товара или колонкой «Доп. услуги» в таблице импорта.</p>
            <?php if (empty($list)): ?>
                <p class="text-muted">Пока ничего нет</p>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead><tr><th width="1%"></th><th>Название</th><th>Цена</th><th>В чеке</th><th>В группах</th><th>Показывать</th><th class="text-end">Порядок</th><th width="1%"></th></tr></thead>
                    <tbody>
                    <?php foreach ($list as $addon): $inGroups = array_filter($groups, fn($g) => in_array((int)$addon->id, $g->addonIds(), true)) ?>
                        <tr class="<?= $addon->is_active ? '' : 'text-muted' ?>">
                            <td><?php if ($addon->imageUrl()): ?><img src="<?= $addon->imageUrl() ?>" alt="" width="48" height="48" class="rounded"><?php endif ?></td>
                            <td><a href="<?= SiteRoot("_admin/shop/addons/add_or_edit&id={$addon->id}") ?>"><?= $addon->name ?></a></td>
                            <td class="text-nowrap"><?= $addon->priceText() ?></td>
                            <td><?= $addon->is_service ? 'Услуга' : 'Товар' ?></td>
                            <td class="small"><?= $inGroups ? implode(', ', array_map(fn($g) => $g->name, $inGroups)) : '—' ?></td>
                            <td><?= $addon->is_active ? 'Да' : 'Нет' ?></td>
                            <td class="text-end"><?= $addon->sort ?></td>
                            <td class="text-nowrap">
                                <a href="<?= SiteRoot("_admin/shop/addons/add_or_edit&id={$addon->id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
                                <?= PostActionButton('delete', ['id' => $addon->id], 'Удалить? Из групп она тоже уберётся', 'btn btn-sm btn-danger rounded-pill') ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>
