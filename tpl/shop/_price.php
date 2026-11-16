<span class="price">
    <span class="price-current"><?= ShopPrice($product->price) ?></span>
    <?php if ($product->hasDiscount()): ?>
        <s class="price-old"><?= ShopPrice($product->price_old) ?></s>
    <?php endif ?>
</span>
