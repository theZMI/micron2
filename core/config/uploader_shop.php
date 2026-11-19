<?php

// Загрузка картинок в админке магазина (_ajax_upload + обрезка _crop_image). core/config/shop.php подключается раньше (по алфавиту)
[$ratioW, $ratioH] = Config('shop')['image_ratio'];
$previewH = (int)round(400 * $ratioH / $ratioW);
Config(
    ['uploader', 'shop_products'], // Картинки товара: загружаются сюда, после обрезки переносятся в ShopImage (upl/shop/products/{id}/)
    [
        'upload_path'   => BASEPATH . 'upl/shop/upload/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'max_size'      => 30000,
        'thumbs'        => [
            ['width' => 400, 'height' => $previewH, 'path' => BASEPATH . "upl/shop/upload/400x{$previewH}/"], // Превью в админке, пропорция - Config('shop')['image_ratio']
        ],
    ]
);
Config(
    ['uploader', 'shop_addons'], // Картинки доп. услуг: квадрат 200x200 (ShopAddonModel::IMAGE_SIZE)
    [
        'upload_path'   => BASEPATH . 'upl/shop/addons/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'max_size'      => 30000,
        'thumbs'        => [
            ['width' => 200, 'height' => 200, 'path' => BASEPATH . 'upl/shop/addons/200x200/'],
        ],
    ]
);
