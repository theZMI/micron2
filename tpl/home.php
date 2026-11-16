<section class="hero">
    <div class="hero-text">
        <span class="hero-eyebrow">Micron · PHP framework</span>
        <h1 class="hero-title">Простой фреймворк<br>с готовым магазином</h1>
        <p class="hero-lead">Компонентная модель (HMVC), каталог с поиском и фильтрами, корзина, избранное и оплата через ЮKassa или Сбер — из коробки.</p>
        <div class="hero-actions">
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-dark btn-lg">Перейти в каталог</a>
            <a href="<?= SiteRoot('examples/engine') ?>" class="btn btn-link link-dark btn-lg">Как устроен движок <i class="bi bi-arrow-right"></i></a>
        </div>
    </div>
</section>

<?php if ($products): ?>
    <section class="home-section">
        <div class="section-head">
            <h2 class="section-title">Каталог</h2>
            <a href="<?= SiteRoot('shop') ?>" class="link-arrow">Все товары <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="product-grid product-grid-wide">
            <?php foreach ($products as $product): ?>
                <?php IncludeCom('shop/_product_card', ['product' => $product]) ?>
            <?php endforeach ?>
        </div>
    </section>
<?php endif ?>
