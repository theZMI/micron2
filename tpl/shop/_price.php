<?php $unit = $product->priceUnitSuffix() ?>
<span class="price">
    <span class="price-current"><span data-price-amount><?= ShopPrice($product->price) ?></span><?php if ($unit): ?><span class="price-unit"><?= $unit ?></span><?php endif ?></span>
    <?php if ($product->hasDiscount()): ?>
        <s class="price-old"><?= ShopPrice($product->price_old) ?></s>
    <?php endif ?>
</span>
