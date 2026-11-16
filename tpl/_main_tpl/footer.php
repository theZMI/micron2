<footer id="footer">
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="<?= SiteRoot() ?>" class="site-logo-text">Micron</a>
                <p class="text-secondary small mt-2 mb-0">PHP-фреймворк с компонентной моделью (HMVC)</p>
            </div>
            <nav class="footer-links">
                <a href="<?= SiteRoot('shop') ?>">Каталог</a>
                <a href="<?= SiteRoot('shop/favorites') ?>">Избранное</a>
                <a href="<?= SiteRoot('shop/cart') ?>">Корзина</a>
                <a href="<?= SiteRoot('contacts') ?>">Связь</a>
            </nav>
            <div class="footer-contacts">
                <a href="tel:<?= PhoneFilter(SUPPORT_PHONE) ?>"><?= SUPPORT_PHONE ?></a>
                <a href="mailto:<?= SUPPORT_EMAIL ?>"><?= SUPPORT_EMAIL ?></a>
            </div>
        </div>
        <div class="footer-bottom">© <?= date('Y') ?> Micron</div>
    </div>
</footer>
