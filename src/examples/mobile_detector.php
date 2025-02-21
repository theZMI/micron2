<?php

$detect = new \Detection\MobileDetect();
echo 'User agent info:<br>';
echo $detect->getUserAgent() . '<hr>';

try {
    $isMobile = $detect->isMobile(); // bool(false)
    echo 'isMobile=' . intval($isMobile) . '<hr>';
} catch (\Detection\Exception\MobileDetectException $e) {
}

try {
    $isTablet = $detect->isTablet(); // bool(false)
    echo 'isTable=' . intval($isTablet) . '<hr>';
} catch (\Detection\Exception\MobileDetectException $e) {
}