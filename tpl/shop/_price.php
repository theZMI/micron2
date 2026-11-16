<?php $sign = Config('shop')['currency_sign'] ?>
<span class="fw-bold fs-5"><?= OutputFormats::amount($product->price, 2, $sign) ?></span>
<?php if ($product->hasDiscount()): ?>
    <s class="text-muted ms-2 small"><?= OutputFormats::amount($product->price_old, 2, $sign) ?></s>
<?php endif ?>
