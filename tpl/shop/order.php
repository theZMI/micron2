<?php $badges = [ShopOrderModel::STATUS_NEW => 'bg-warning text-dark', ShopOrderModel::STATUS_PAID => 'bg-success', ShopOrderModel::STATUS_DONE => 'bg-primary', ShopOrderModel::STATUS_CANCELED => 'bg-secondary'] ?>
<div class="container my-4" style="max-width: 800px;">
    <h1 class="mb-1">Заказ №<?= $order->id ?></h1>
    <div class="mb-4">
        <span class="badge <?= $badges[$order->status] ?? 'bg-secondary' ?>"><?= $order->statusName() ?></span>
        <span class="text-muted ms-2"><?= date('d.m.Y H:i', $order->create_time) ?></span>
    </div>
    <?php IncludeCom('shop/_order_items', ['order' => $order]) ?>
    <dl class="row mt-4">
        <dt class="col-sm-3">Покупатель</dt><dd class="col-sm-9"><?= $order->customer_name ?></dd>
        <dt class="col-sm-3">E-mail</dt><dd class="col-sm-9"><?= $order->customer_email ?></dd>
        <?php if ($order->customer_phone): ?><dt class="col-sm-3">Телефон</dt><dd class="col-sm-9"><?= OutputFormats::mobilePhone($order->customer_phone) ?></dd><?php endif ?>
        <?php if ($order->comment): ?><dt class="col-sm-3">Комментарий</dt><dd class="col-sm-9"><?= nl2br($order->comment) ?></dd><?php endif ?>
    </dl>
    <?php if ($order->canPay()): ?>
        <div class="text-center">
            <a href="<?= $order->url('pay_system/pay_form') ?>" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-credit-card pe-2"></i>Оплатить</a>
        </div>
    <?php endif ?>
</div>
