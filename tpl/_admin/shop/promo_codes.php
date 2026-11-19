<div class="container-fluid">
    <div class="row">
        <div class="col" style="max-width: 1100px;">
            <h1 class="my-4">
                <span class="pull-start me-3">Промокоды</span>
                <a href="<?= SiteRoot('_admin/shop/promo_codes/add_or_edit') ?>" class="btn btn-primary rounded-pill pull-end"><i class="bi bi-plus-lg me-2"></i>Добавить</a>
            </h1>
            <?php if (empty($list)): ?>
                <p class="text-muted">Промокодов пока нет. Покупатель вводит промокод в корзине, скидка действует на товары (не на доставку).</p>
            <?php else: ?>
                <table class="table table-hover align-middle">
                    <thead><tr><th>Код</th><th>Скидка</th><th>От суммы</th><th>Использован</th><th>Действует</th><th>Комментарий</th><th width="1%"></th></tr></thead>
                    <tbody>
                    <?php foreach ($list as $promo): $error = $promo->checkError(PHP_INT_MAX) ?>
                        <tr class="<?= $error ? 'text-muted' : '' ?>">
                            <td><a href="<?= SiteRoot("_admin/shop/promo_codes/add_or_edit&id={$promo->id}") ?>"><b><?= $promo->code ?></b></a></td>
                            <td><?= $promo->valueText() ?></td>
                            <td><?= $promo->min_amount > 0 ? ShopPrice($promo->min_amount) : '—' ?></td>
                            <td><?= $promo->used_count ?><?= $promo->max_uses ? ' из ' . $promo->max_uses : '' ?></td>
                            <td class="small">
                                <?= $promo->start_time ? 'с ' . date('d.m.Y', $promo->start_time) : '' ?>
                                <?= $promo->end_time ? 'по ' . date('d.m.Y', $promo->end_time) : ($promo->start_time ? '' : 'бессрочно') ?>
                                <?php if ($error): ?><div class="text-danger"><?= $error ?></div><?php endif ?>
                            </td>
                            <td class="small"><?= $promo->comment ?></td>
                            <td class="text-nowrap">
                                <a href="<?= SiteRoot("_admin/shop/promo_codes/add_or_edit&id={$promo->id}") ?>" class="btn btn-sm btn-primary rounded-pill" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
                                <?= PostActionButton('delete', ['id' => $promo->id], 'Удалить промокод?', 'btn btn-sm btn-danger rounded-pill') ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </div>
</div>
