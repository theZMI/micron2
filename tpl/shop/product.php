<div class="shop-page">
    <?php IncludeCom('shop/_product_details', ['product' => $product, 'full' => true]) ?>

    <?php if ($boughtTogether): ?>
        <section class="home-section">
            <div class="section-head"><h2 class="section-title">Покупают вместе</h2></div>
            <div class="product-grid product-grid-wide">
                <?php foreach ($boughtTogether as $item): ?>
                    <?php IncludeCom('shop/_product_card', ['product' => $item]) ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>

    <?php if ($recentlyViewed): ?>
        <section class="home-section">
            <div class="section-head"><h2 class="section-title">Вы недавно смотрели</h2></div>
            <div class="product-grid product-grid-wide">
                <?php foreach ($recentlyViewed as $item): ?>
                    <?php IncludeCom('shop/_product_card', ['product' => $item]) ?>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>
</div>
