<?php

use WideImage\WideImage;

Config('is_load_in_main_tpl', false);

$field              = Get('field');
$defH               = intval(Get('def_h'));
$defW               = intval(Get('def_w'));
$hasDefHW           = $defH && $defW;
$aspectRatio        = $defH && $defW ? floatval($defW / $defH) : 1;
$uri                = Get('uri');
$url                = Root($uri . '&random=' . time());
$path               = realpath(BASEPATH . $uri);
$aPath              = pathinfo($path);
$hasThumbs          = Get('has_thumbs');
$sThumbs            = Get('thumbs');
$thumbs             = [];
$accessDirs         = [BASEPATH . 'upl/'];
$isImageInAccessDir = false;

if (strlen($sThumbs)) {
    foreach (explode('|', $sThumbs) as $w_and_h) {
        $t = [];
        foreach (explode('x', $w_and_h) as $w_or_h) {
            $t[] = $w_or_h;
        }
        $thumbs[] = $t;
    }
}

foreach ($accessDirs as $dir) {
    if (stripos("{$aPath['dirname']}/", $dir) !== false) {
        $isImageInAccessDir = true;
        break;
    }
}

if (!$isImageInAccessDir) {
    trigger_error("Access denied", E_USER_ERROR);
}

if (Post('is_set')) {
    $x         = intval(Post('x'));
    $y         = intval(Post('y'));
    $w         = intval(Post('w'));
    $h         = intval(Post('h'));
    $visible_w = intval(Post('visible_w')) ?: 1;
    $imageInfo = getimagesize($path);
    $scaleCoef = floatval($imageInfo[0] / $visible_w);
    $needX     = intval($x * $scaleCoef);
    $needY     = intval($y * $scaleCoef);
    $needW     = intval($w * $scaleCoef);
    $needH     = intval($h * $scaleCoef);
    $image     = WideImage::load($path);
    $cropped   = $image->crop($needX, $needY, $needW, $needH);
    $cropped->saveToFile($path);
    foreach ($thumbs as $t) {
        $oneThumbs = $aPath['dirname'] . "/" . $t[0] . 'x' . $t[1] . '/' . $aPath['basename'];
        Uploader::CreateThumbs($path, $oneThumbs, $t[0], $t[1]);
    }

    exit('1');
}