<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Товар</th><th class="text-end">Цена</th><th class="text-end">Кол-во</th><th class="text-end">Сумма</th></tr></thead>
        <tbody>
        <?php foreach ($order->getItems() as $item): ?>
            <tr class="<?= $item->addon_id ? 'small' : '' ?>">
                <?php if ($item->addon_id): // Доп. услуга к товару выше ?>
                    <td class="ps-4 text-muted"><i class="bi bi-plus"></i><?= $item->name ?></td>
                <?php else: ?>
                    <td><?= $item->name ?><?php if ($item->vendor_code): ?><div class="small text-muted"><?= $item->vendor_code ?></div><?php endif ?></td>
                <?php endif ?>
                <td class="text-end text-nowrap">
                    <?= ShopPrice($item->price) ?>
                    <?php if ($item->hasDiscount()): ?><div class="small text-muted"><s><?= ShopPrice($item->price_full) ?></s></div><?php endif ?>
                </td>
                <td class="text-end text-nowrap"><?= ShopQuantity($item->quantity, $item->unit ?: 'шт') ?></td>
                <td class="text-end text-nowrap"><?= ShopPrice($item->amount) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot>
        <?php if ($order->discount_amount > 0 || $order->delivery_price > 0 || $order->delivery_name !== ''): ?>
            <tr><td colspan="3" class="text-end">Товары:</td><td class="text-end text-nowrap"><?= ShopPrice($order->items_amount) ?></td></tr>
            <?php if ($order->discount_amount > 0): ?>
                <tr><td colspan="3" class="text-end">Скидка по промокоду <?= $order->promo_code ?>:</td><td class="text-end text-nowrap">−<?= ShopPrice($order->discount_amount) ?></td></tr>
            <?php endif ?>
            <?php if ($order->delivery_name !== ''): ?>
                <tr><td colspan="3" class="text-end">Доставка (<?= $order->delivery_name ?>):</td><td class="text-end text-nowrap"><?= $order->delivery_price > 0 ? ShopPrice($order->delivery_price) : 'бесплатно' ?></td></tr>
            <?php endif ?>
        <?php endif ?>
        <tr><th colspan="3" class="text-end">Итого:</th><th class="text-end text-nowrap"><?= ShopPrice($order->total_amount) ?></th></tr>
        </tfoot>
    </table>
</div>
