<form action="<?= SiteRoot('shop/cart') ?>" method="post" class="d-flex gap-2">
    <input type="hidden" name="a" value="add">
    <input type="hidden" name="product_id" value="<?= $product->id ?>">
    <input type="number" name="quantity" value="1" min="1" max="<?= Config('shop')['max_quantity'] ?>" class="form-control" style="max-width: 90px;">
    <button type="submit" class="btn btn-primary rounded-pill flex-grow-1"><i class="bi bi-cart-plus pe-2"></i>В корзину</button>
</form>
