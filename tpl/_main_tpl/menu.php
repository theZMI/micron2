<?php if (IS_USER_AUTH): ?>
    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasUserMenu" aria-labelledby="offcanvasUserMenuLabel">
        <div class="offcanvas-header">
            <div>
                <div class="text-secondary small">Личный кабинет</div>
                <h5 class="offcanvas-title" id="offcanvasUserMenuLabel"><?= $g_user->full_name ?: $g_user->email ?></h5>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
        </div>
        <div class="offcanvas-body">
            <nav class="offcanvas-menu-list">
                <?php foreach ($userMenu as $item): ?>
                    <a href="<?= $item['link'] ?>"><?= $item['name'] ?></a>
                <?php endforeach ?>
            </nav>
        </div>
    </div>
<?php endif ?>
<?php IncludeCom('_dev/menu', ['logo' => $logo, 'menu' => $menu, 'rightMenu' => $rightMenu, 'incSearch' => true, 'mobileIcons' => $mobileIcons]) ?>

<!-- Быстрый просмотр товара (заполняется из JS, см. i/webpack/src/shop.js) -->
<div class="modal fade" id="quickViewModal" data-url="<?= SiteRoot('shop/quick_view') ?>" tabindex="-1" aria-label="Быстрый просмотр" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <button type="button" class="btn-close quick-view-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            <div class="modal-body" data-quick-view-body></div>
        </div>
    </div>
</div>
<div class="toast-container position-fixed bottom-0 end-0 p-3" data-toasts data-cart-url="<?= SiteRoot('shop/cart') ?>" data-favorites-url="<?= SiteRoot('shop/favorites') ?>"></div>
