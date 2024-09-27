<?php

$msg = '';
ob_start();
IncludeCom(
    '_ajax_upload',
    [
        'uplName'       => 'image_1',
        'uplConf'       => Config(['uploader', 'examples']), // Сливается с дефолтным конфигом в core/config/uploader.php
        'isImageUpload' => true, // Если это картинка, то после загрузки откроется окошко предлагающее сделать crop изображения
        'defWidth'      => 640, // defWidth и defHeight определяют дефолтные размеры квадратика для crop-а
        'defHeight'     => 480
    ]
);
$preview_image_form = ob_get_clean();