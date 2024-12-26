<?php
sleep(4);
$helloMessage = "Я жив! Сейчас: " . gmdate("d-m-Y H:i:s") . ' UTC';
(new ApiResponse())->normal(
    $_SERVER['REQUEST_METHOD'] . ": {$helloMessage}"
);