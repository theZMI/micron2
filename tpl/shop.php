<div class="container my-4">
    <h1 class="mb-4 d-flex align-items-center">
        <span class="me-auto">Каталог</span>
        <a href="<?= SiteRoot('shop/cart') ?>" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-cart3 pe-2"></i>Корзина<?php if ($count = ShopCart::count()): ?> <span class="badge bg-primary ms-1"><?= $count ?></span><?php endif ?>
        </a>
    </h1>
    <?php if (empty($products)): ?>
        <p class="text-muted">Товаров пока нет. Добавьте их в админке: <a href="<?= SiteRoot('_admin/shop/products') ?>">Магазин → Товары</a></p>
    <?php endif ?>
    <div class="row g-4">
        <?php foreach ($products as $product): ?>
            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><a href="<?= SiteRoot("shop/product&id={$product->id}") ?>" class="text-decoration-none"><?= $product->name ?></a></h5>
                        <?php if ($product->vendor_code): ?>
                            <div class="small text-muted mb-2">Артикул: <?= $product->vendor_code ?></div>
                        <?php endif ?>
                        <div class="mt-auto mb-3"><?php IncludeCom('shop/_price', ['product' => $product]) ?></div>
                        <?php IncludeCom('shop/_add_to_cart_form', ['product' => $product]) ?>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
    <div class="mt-4">
        <?php
        IncludeCom('_dev/paginator', [
            'css'          => 'justify-content-center',
            'pageUrl'      => GetCurUrl('page=' . M_PAGINATOR_PAGE),
            'firstPageUrl' => GetCurUrl('page=' . M_DELETE_PARAM),
            'total'        => $total,
            'perPage'      => $perPage,
            'curPage'      => $curPage,
        ]);
        ?>
    </div>
</div>
