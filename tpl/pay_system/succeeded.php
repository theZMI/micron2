<div class="container my-5 text-center" style="max-width: 640px;">
    <i class="bi bi-check-circle-fill text-success" style="font-size: 64px;"></i>
    <h1 class="h3 mt-3">Заказ №<?= $order->id ?> оплачен</h1>
    <p class="text-muted">Спасибо! Подтверждение отправили на <?= $order->customer_email ?>.</p>
    <a href="<?= $order->url() ?>" class="btn btn-primary rounded-pill">Посмотреть заказ</a>
</div>
