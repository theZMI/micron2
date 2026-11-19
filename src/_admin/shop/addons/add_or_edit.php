<?php

$model      = new ShopAddonModel(intval(Get('id')) ?: null);
$modelParam = fn($param, $default = '') => ModelParam($model, $param, Post($param, $default));

// Картинка: онлайн-загрузчик (_ajax_upload) с обрезкой в квадрат, готовая 200x200 - в upl/shop/addons/200x200/
ob_start();
IncludeCom('_ajax_upload', [
    'uplName'       => 'addon_image',
    'uplConf'       => Config(['uploader', 'shop_addons']),
    'isImageUpload' => true,
    'defWidth'      => ShopAddonModel::IMAGE_SIZE,
    'defHeight'     => ShopAddonModel::IMAGE_SIZE,
]);
$uploadForm = ob_get_clean();

$msg = '';
if (Post('is_set')) {
    $name  = trim((string)Post('name'));
    $type  = array_key_exists((string)Post('price_type'), ShopAddonModel::priceTypes()) ? (string)Post('price_type') : ShopAddonModel::PRICE_FIXED;
    $value = ShopKopecks((string)Post('price_value', '0')) ?? -1; // Рубли -> копейки, проценты -> сотые доли процента (оба ×100)
    $image = basename((string)Post('addon_image_rdy_name', ''));
    $errs  = [];
    if ($name === '') {
        $errs[] = 'Впишите название';
    }
    if ($value < 0 || ($type === ShopAddonModel::PRICE_PERCENT && ($value <= 0 || $value > 100000))) {
        $errs[] = $type === ShopAddonModel::PRICE_PERCENT ? 'Процент - больше 0' : 'Цена не может быть отрицательной';
    }
    if ($image !== '' && !ShopAddonModel::isUploadedImage($image)) {
        $errs[] = 'Картинка не загрузилась, попробуйте ещё раз';
    }

    if ($errs) {
        $msg = MsgErr(implode('<br>', $errs));
    } else {
        if ($image !== '' && $model->image !== '' && $model->image !== $image) {
            // Старая картинка больше не нужна
            @unlink(BASEPATH . ShopAddonModel::UPLOAD_DIR . basename($model->image));
            @unlink(BASEPATH . ShopAddonModel::UPLOAD_DIR . '200x200/' . basename($model->image));
        }
        $model->name        = $name;
        $model->description = (string)Post('description');
        $model->price_type  = $type;
        $model->price_value = $value;
        $model->is_service  = (bool)Post('is_service');
        $model->is_active   = (bool)Post('is_active');
        $model->sort        = intval(Post('sort'));
        if ($image !== '') {
            $model->image = $image;
        } elseif (Post('image_delete')) {
            $model->image = '';
        }
        $model->flush();
        UrlRedirect::go(SiteRoot('_admin/shop/addons'));
    }
}
