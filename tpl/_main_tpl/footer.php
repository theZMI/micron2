<footer id="footer">
    <div class="container">
        <div class="row mt-4 mb-3">
            <div class="col">
                <a href="<?= SiteRoot() ?>"><span>Главная</span></a>
                <a href="<?= SiteRoot('catalog') ?>"><span>Каталог товаров</span></a>
                <a href="<?= SiteRoot('contacts') ?>"><span>Контакты</span></a>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <span class="float-start mb-4">
                    Micron - php framework<br>
                    Все права защищены <?= date("Y") ?> ©
                </span>
                <span class="float-end">
                    <a href="mailto:<?= SUPPORT_EMAIL ?>"><span><?= SUPPORT_EMAIL ?></span></a>
                    &nbsp;
                    <a href="tel:<?= PhoneFilter(SUPPORT_PHONE) ?>"><span><?= SUPPORT_PHONE ?></span></a>
                </span>
            </div>
        </div>
    </div>
</footer>
