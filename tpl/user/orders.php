<h1 class="my-4">Мои заказы</h1>
<?php if (empty($orders)): ?>
    <p class="text-muted">Заказов пока нет. <a href="<?= SiteRoot('shop') ?>">Перейти в каталог</a></p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>№</th><th>Дата</th><th class="text-end">Сумма</th><th>Статус</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= $order->id ?></td>
                    <td><?= date('d.m.Y H:i', $order->create_time) ?></td>
                    <td class="text-end"><?= ShopPrice($order->total_amount) ?></td>
                    <td><?= $order->statusName() ?></td>
                    <td class="text-end"><a href="<?= $order->url() ?>" class="btn btn-sm btn-outline-primary rounded-pill">Открыть</a></td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
