<h1>Платёжная форма</h1>
<form action="<?= GetCurUrl() ?>" method="post">
    <input type="hidden" name="is_set" value="1">
    <div class="mb-3">
        <label class="form-label">Название товара</label>
        <input type="text" class="form-control" name="item_name" value="Наклейка №1001">
    </div>
    <div class="mb-3">
        <label class="form-label">Артикул товара</label>
        <input type="text" class="form-control" name="item_vendor_code" value="item-1001">
    </div>
    <div class="input-group mb-3">
        <span class="input-group-text">₽</span>
        <input type="text" class="form-control" name="item_price" value="2.50">
    </div>
    <button type="submit" class="btn btn-primary">Купить</button>
</form>