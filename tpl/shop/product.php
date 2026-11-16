<div class="container my-4" style="max-width: 800px;">
    <h1 class="my-3"><?= $product->name ?></h1>
    <?php if ($product->vendor_code): ?>
        <div class="text-muted mb-3">Артикул: <?= $product->vendor_code ?></div>
    <?php endif ?>
    <div class="fs-3 fw-bold mb-4"><?= OutputFormats::amount($product->price, 2, Config('shop')['currency_sign']) ?></div>
    <div class="mb-4" style="max-width: 360px;">
        <?php IncludeCom('shop/_add_to_cart_form', ['product' => $product]) ?>
    </div>
    <?php if ($product->description): ?>
        <div><?= nl2br($product->description) ?></div>
    <?php endif ?>
</div>
