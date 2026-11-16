<div class="container my-5 text-center" style="max-width: 640px;">
    <?= $msg ?>
    <div class="spinner-border text-primary mb-3" role="status"></div>
    <h1 class="h3">Ждём ответа банка</h1>
    <p class="text-muted">Заказ №<?= $order->id ?>. Обычно это занимает несколько секунд.</p>
    <a href="<?= GetCurUrl() ?>" class="btn btn-primary rounded-pill"><i class="bi bi-arrow-clockwise pe-2"></i>Обновить</a>
    <?php if ($payment->pay_link): ?>
        <a href="<?= htmlspecialchars($payment->pay_link) ?>" class="btn btn-link">Вернуться к оплате</a>
    <?php endif ?>
</div>
<script>setTimeout(() => window.location.reload(), 5000);</script>
