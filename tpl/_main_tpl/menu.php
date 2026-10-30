<div id="extra-header">
    <div class="container">
        <div class="row">
            <div class="col">
                <?php $geo = GetLocationByIP(); ?>
                <?php if (!empty($geo['countryCode'])): ?>
                <div class="float-start">
                    <button class="btn btn-link border-0 ps-0 geo-position-button" type="button">
                        <div style="background-image: url('https://flagcdn.com/<?= strtolower($geo['countryCode']) ?>.svg')" class="geo-position-button-flag align-top me-1"></div>
                        <span><?= htmlspecialchars((string)$geo['city']) ?></span>
                        <span class="text-black-50">(<?= htmlspecialchars((string)$geo['country']) ?>)</span>
                    </button>
                </div>
                <?php endif; ?>
                <div class="float-end">
                    <div class="fast-contacts-info d-none d-sm-block">
                        <a href="tel:<?= PhoneFilter(SUPPORT_PHONE) ?>" class="btn btn-link border-0"><span><?= SUPPORT_PHONE ?></span></a>
                        <span>&nbsp;&bull;&nbsp;</span>
                        <a href="mailto:<?= SUPPORT_EMAIL ?>" class="btn btn-link border-0 pe-0"><span><?= SUPPORT_EMAIL ?></span></a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserMenu" aria-labelledby="offcanvasUserMenuLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasUserMenuLabel">Личный кабинет:</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div class="offcanvas-menu-list">
            <ul class="list-unstyled">
                <?php foreach ($userMenu as $item): ?>
                    <li><a href="<?= $item['link'] ?>"><span><?= $item['name'] ?></span></a></li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
</div>
<?php IncludeCom('_dev/menu', ['logo' => $logo, 'menu' => $menu, 'rightMenu' => $rightMenu, 'incSearch' => true]) ?>