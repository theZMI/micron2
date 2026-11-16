<div class="container my-4" style="max-width: 640px;">
    <h1 class="mb-4">Оплата заказа №<?= $order->id ?></h1>
    <?= $msg ?>
    <?php IncludeCom('shop/_order_items', ['order' => $order]) ?>
    <form action="<?= GetCurUrl() ?>" method="post" class="text-center mt-4">
        <input type="hidden" name="is_set" value="1">
        <a href="<?= $order->url() ?>" class="btn btn-link btn-lg"><i class="bi bi-arrow-left pe-2"></i>К заказу</a>
        <button type="submit" class="btn btn-primary btn-lg rounded-pill"><i class="bi bi-credit-card pe-2"></i>Перейти к оплате</button>
    </form>
</div>
