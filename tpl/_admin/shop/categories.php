<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 1000px;">
            <h1 class="my-4">
                <span class="pull-start me-3">Категории</span>
                <a href="<?= SiteRoot('_admin/shop/categories/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end"><i class="bi bi-plus-lg me-2"></i>Добавить</a>
            </h1>
            <?php if (empty($rows)): ?>
                <p class="text-muted">Категорий пока нет. Их можно добавить здесь или колонкой «Категория» в <a href="<?= SiteRoot('_admin/shop/import') ?>">импорте</a> («Одежда / Футболки» - с подкатегорией).</p>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead><tr><th>Название</th><th>Адрес</th><th class="text-end">Товаров</th><th>Показывать</th><th class="text-end">Порядок</th><th width="1%"></th></tr></thead>
                    <tbody>
                    <?php foreach ($options as $id => $title): $row = $rows[$id] ?>
                        <tr>
                            <td><a href="<?= SiteRoot("_admin/shop/categories/add_or_edit&id={$id}") ?>"><?= $title ?></a></td>
                            <td><a href="<?= ShopCategoryModel::urlById($id) ?>" target="_blank" class="small">/shop/c/<?= $row['slug'] ?></a></td>
                            <td class="text-end"><?= $counts[$id] ?? 0 ?></td>
                            <td><?= $row['is_active'] ? 'Да' : '<span class="text-muted">Нет</span>' ?></td>
                            <td class="text-end"><?= $row['sort'] ?></td>
                            <td class="text-nowrap">
                                <a href="<?= SiteRoot("_admin/shop/categories/add_or_edit&id={$id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
                                <?= PostActionButton('delete', ['id' => $id], 'Удалить категорию? Подкатегории перейдут на уровень выше, товары останутся без категории', 'btn btn-sm btn-danger rounded-pill') ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>
