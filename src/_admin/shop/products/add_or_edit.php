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

// Загруженные файлы из <input type="file" name="images[]" multiple>, возвращает ошибки
$saveUploadedImages = function (ShopProductModel $model): array {
    $files = $_FILES['images'] ?? null;
    $errs  = [];
    foreach ((array)($files['tmp_name'] ?? []) as $i => $tmp) {
        $error = $files['error'][$i] ?? UPLOAD_ERR_NO_FILE;
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $fileName = htmlspecialchars((string)$files['name'][$i]);
        if ($error !== UPLOAD_ERR_OK || !is_uploaded_file($tmp)) {
            $errs[] = "{$fileName}: файл не загрузился (код {$error})";
            continue;
        }
        try {
            $model->addImageFromFile($tmp);
        } catch (Throwable $e) {
            $errs[] = "{$fileName}: " . htmlspecialchars($e->getMessage());
        }
    }
    return $errs;
};

if (Post('is_set')) {
    $name     = trim((string)Post('name'));
    $price    = OutputFormats::fromAmount((string)Post('price', '0'));
    $priceOld = OutputFormats::fromAmount((string)Post('price_old', '0'));
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
        $model->price       = ShopOrderModel::toCents($price) / 100;
        $model->price_old   = ShopOrderModel::toCents(max(0, $priceOld)) / 100;
        $model->description = (string)Post('description');
        $model->is_active   = (bool)Post('is_active');
        $model->sort        = intval(Post('sort'));
        $model->variant_group = trim((string)Post('variant_group'));
        $model->related       = implode(', ', array_filter(array_map('trim', explode(',', (string)Post('related'))), 'strlen'));
        $stock                = trim((string)Post('stock'));
        $model->track_stock   = $stock !== ''; // Пусто - остаток не учитываем
        $model->stock         = $stock === '' ? 0 : max(0, (int)$stock);
        $model->setParams($paramsFromText((string)Post('params_text', '')));
        $id                 = $model->flush();
        $imageErrs          = $saveUploadedImages($model);
        $model->flush();
        if ($imageErrs) {
            $msg = MsgWarning('Товар сохранён, но не все картинки загружены:<br>' . implode('<br>', $imageErrs));
        } else {
            UrlRedirect::go(SiteRoot("_admin/shop/products&sel_id={$id}"));
        }
    }
}
