<div class="container my-5 text-center" style="max-width: 640px;">
    <i class="bi bi-x-circle-fill text-danger" style="font-size: 64px;"></i>
    <h1 class="h3 mt-3">Оплата заказа №<?= $order->id ?> не прошла</h1>
    <p class="text-muted">Деньги не списаны. Можно попробовать ещё раз или другой картой.</p>
    <?php if ($order->canPay()): ?>
        <a href="<?= $order->url('pay_system/pay_form') ?>" class="btn btn-primary rounded-pill">Оплатить ещё раз</a>
    <?php endif ?>
    <a href="<?= $order->url() ?>" class="btn btn-link">К заказу</a>
</div>
