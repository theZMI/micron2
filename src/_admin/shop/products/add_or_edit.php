<?php

$model      = new ShopProductModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));

// Характеристики в форме - по строке на каждую: «Состав: 100% хлопок»
$paramsFromText = function (string $text): array {
    $params = [];
    foreach (preg_split('/\R/', $text) as $line) {
        [$name, $value] = array_map('trim', explode(':', $line, 2)) + [1 => ''];
        if ($name !== '' && $value !== '') {
            $params[$name] = $value;
        }
    }
    return $params;
};
$paramsText = Post('params_text') !== false
    ? (string)Post('params_text')
    : implode("\n", array_map(fn($k, $v) => "{$k}: {$v}", array_keys($model->getParams()), $model->getParams()));

$msg = '';

// Картинки: сделать главной / удалить (отдельные формы на странице товара)
if (in_array(Post('a'), ['image_main', 'image_delete'], true) && $model->isExists()) {
    $image = (string)Post('image');
    Post('a') === 'image_main' ? $model->setMainImage($image) : $model->removeImage($image);
    $model->flush();
    UrlRedirect::go(GetCurUrl());
}

// Картинки: онлайн-загрузчик (_ajax_upload) с обрезкой в пропорции Config('shop')['image_ratio']. После обрезки страница сама
// отправляет форму a=image_add: файл переносится в картинки товара (ShopImage уменьшает его до 1600px и делает превью)
[$ratioW, $ratioH] = Config('shop')['image_ratio'];
$previewW = 400;
$previewH = (int)round($previewW * $ratioH / $ratioW);
ob_start();
IncludeCom('_ajax_upload', [
    'uplName'       => 'product_image',
    'uplConf'       => Config(['uploader', 'shop_products']),
    'isImageUpload' => true,
    'defWidth'      => $ratioW * 100,
    'defHeight'     => $ratioH * 100,
]);
$uploadForm = ob_get_clean();

if (Post('a') === 'image_add' && $model->isExists()) {
    $name = basename((string)Post('product_image_rdy_name', ''));
    $dir  = Config(['uploader', 'shop_products'])['upload_path'];
    if ($name !== '' && is_file($dir . $name)) {
        try {
            $model->addImageFromFile($dir . $name);
            $model->flush();
        } catch (Throwable $e) {
            $msg = MsgErr('Картинка не добавилась: ' . htmlspecialchars($e->getMessage()));
        }
        // Загруженный файл и его превью больше не нужны
        @unlink($dir . $name);
        @unlink($dir . "{$previewW}x{$previewH}/" . $name);
    }
    if ($msg === '') {
        UrlRedirect::go(GetCurUrl());
    }
}

if (Post('is_set')) {
    $name     = trim((string)Post('name'));
    $price    = (int)ShopKopecks((string)Post('price', '0')); // Вводят в рублях, храним в копейках
    $priceOld = (int)ShopKopecks((string)Post('price_old', '0'));
    $errs     = [];
    if ($name === '') {
        $errs[] = 'Впишите название товара';
    }
    if ($price <= 0) {
        $errs[] = 'Цена должна быть больше нуля';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        $model->name        = $name;
        $model->vendor_code = trim((string)Post('vendor_code'));
        $model->price       = $price;
        $model->price_old   = max(0, $priceOld);
        $model->description = (string)Post('description');
        $model->is_active   = (bool)Post('is_active');
        $model->sort        = intval(Post('sort'));
        $model->variant_group = trim((string)Post('variant_group'));
        $model->related       = implode(', ', array_filter(array_map('trim', explode(',', (string)Post('related'))), 'strlen'));
        $model->similar       = implode(', ', array_filter(array_map('trim', explode(',', (string)Post('similar'))), 'strlen'));
        $stock                = trim(str_replace(',', '.', (string)Post('stock')));
        $model->track_stock   = $stock !== ''; // Пусто - остаток не учитываем
        $model->stock         = $stock === '' ? 0 : max(0, round((float)$stock, 3));
        $model->category_id   = isset(ShopCategoryModel::rows()[intval(Post('category_id'))]) ? intval(Post('category_id')) : 0;
        $model->unit          = array_key_exists((string)Post('unit'), Config('shop')['units']) ? (string)Post('unit') : Config('shop')['default_unit'];
        $model->qty_step      = max(ShopProductModel::unitMinStep($model->unit), round((float)str_replace(',', '.', (string)Post('qty_step', '0')), 3)); // Не меньше min_step единицы
        $model->qty_min       = max(0, round((float)str_replace(',', '.', (string)Post('qty_min', '0')), 3));
        $model->setParams($paramsFromText((string)Post('params_text', '')));
        $model->setAddonGroupIds(array_map('intval', (array)Post('addon_group_ids', [])));
        $isNew = !$model->isExists();
        $id    = $model->flush();
        ShopStockSubscriptionModel::notifyAvailable((int)$id); // Товар снова в наличии - пишем тем, кто ждал
        // Новый товар - открываем его карточку, чтобы добавить картинки (они загружаются к сохранённому товару)
        UrlRedirect::go($isNew ? SiteRoot("_admin/shop/products/add_or_edit&id={$id}&is_new=1") : SiteRoot("_admin/shop/products&sel_id={$id}"));
    }
}
