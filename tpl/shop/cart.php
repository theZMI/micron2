<?php $sign = Config('shop')['currency_sign'] ?>
<div class="container my-4" style="max-width: 900px;">
    <h1 class="mb-4">Корзина</h1>
    <?php if (empty($items)): ?>
        <div class="text-center text-muted my-5">
            <i class="bi bi-cart3" style="font-size: 48px;"></i>
            <p class="mt-3">Корзина пуста</p>
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-primary rounded-pill">В каталог</a>
        </div>
    <?php else: ?>
        <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-form">
            <input type="hidden" name="a" value="set">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                    <tr><th>Товар</th><th class="text-end">Цена</th><th style="width: 110px;">Кол-во</th><th class="text-end">Сумма</th><th style="width: 1%;"></th></tr>
                    </thead>
                    <tbody>
                    <?php foreach ($items as $item): $product = $item['product'] ?>
                        <tr>
                            <td>
                                <a href="<?= SiteRoot("shop/product&id={$product->id}") ?>"><?= $product->name ?></a>
                                <?php if ($product->vendor_code): ?><div class="small text-muted"><?= $product->vendor_code ?></div><?php endif ?>
                            </td>
                            <td class="text-end"><?= OutputFormats::amount($product->price, 2, $sign) ?></td>
                            <td><input type="number" name="quantity[<?= $product->id ?>]" value="<?= $item['quantity'] ?>" min="0" max="<?= Config('shop')['max_quantity'] ?>" class="form-control form-control-sm" onchange="this.form.submit()"></td>
                            <td class="text-end"><?= OutputFormats::amount($item['amount'], 2, $sign) ?></td>
                            <td>
                                <button type="submit" form="cart-remove-<?= $product->id ?>" class="btn btn-sm btn-outline-danger rounded-pill" title="Удалить"><i class="bi bi-trash3"></i></button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                    <tfoot>
                    <tr><th colspan="3" class="text-end">Итого:</th><th class="text-end"><?= OutputFormats::amount($total, 2, $sign) ?></th><th></th></tr>
                    </tfoot>
                </table>
            </div>
            <noscript><button type="submit" class="btn btn-secondary">Пересчитать</button></noscript>
        </form>
        <?php foreach ($items as $item): ?>
            <form action="<?= SiteRoot('shop/cart') ?>" method="post" id="cart-remove-<?= $item['product']->id ?>" class="d-none">
                <input type="hidden" name="a" value="remove">
                <input type="hidden" name="product_id" value="<?= $item['product']->id ?>">
            </form>
        <?php endforeach ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <a href="<?= SiteRoot('shop') ?>" class="btn btn-link"><i class="bi bi-arrow-left pe-2"></i>Продолжить покупки</a>
            <a href="<?= SiteRoot('shop/checkout') ?>" class="btn btn-primary btn-lg rounded-pill">Оформить заказ<i class="bi bi-arrow-right ps-2"></i></a>
        </div>
    <?php endif ?>
</div>
