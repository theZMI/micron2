<div class="container my-4" style="max-width: 800px;">
    <h1 class="my-3"><?= $product->name ?></h1>
    <?php if ($product->vendor_code): ?>
        <div class="text-muted mb-3">Артикул: <?= $product->vendor_code ?></div>
    <?php endif ?>
    <div class="fs-4 mb-4"><?php IncludeCom('shop/_price', ['product' => $product]) ?></div>
    <div class="mb-4" style="max-width: 360px;">
        <?php IncludeCom('shop/_add_to_cart_form', ['product' => $product]) ?>
    </div>
    <?php if ($params = $product->getParams()): ?>
        <table class="table table-sm mb-4" style="max-width: 560px;">
            <tbody>
            <?php foreach ($params as $name => $value): ?>
                <tr><th class="text-muted fw-normal" style="width: 40%;"><?= $name ?></th><td><?= $value ?></td></tr>
            <?php endforeach ?>
            </tbody>
        </table>
    <?php endif ?>
    <?php if ($product->description): ?>
        <div><?= nl2br($product->description) ?></div>
    <?php endif ?>
</div>
