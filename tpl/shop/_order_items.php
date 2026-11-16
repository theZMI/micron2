<?php $sign = Config('shop')['currency_sign'] ?>
<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Товар</th><th class="text-end">Цена</th><th class="text-end">Кол-во</th><th class="text-end">Сумма</th></tr></thead>
        <tbody>
        <?php foreach ($order->getItems() as $item): ?>
            <tr>
                <td><?= $item->name ?><?php if ($item->vendor_code): ?><div class="small text-muted"><?= $item->vendor_code ?></div><?php endif ?></td>
                <td class="text-end"><?= OutputFormats::amount($item->price, 2, $sign) ?></td>
                <td class="text-end"><?= $item->quantity ?></td>
                <td class="text-end"><?= OutputFormats::amount($item->amount, 2, $sign) ?></td>
            </tr>
        <?php endforeach ?>
        </tbody>
        <tfoot><tr><th colspan="3" class="text-end">Итого:</th><th class="text-end"><?= OutputFormats::amount($order->total_amount, 2, $sign) ?></th></tr></tfoot>
    </table>
</div>
