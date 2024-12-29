<?php

$helloMessage = "Эмулирую ошибку. Сейчас: " . gmdate("d-m-Y H:i:s") . ' UTC';
(new ApiResponse())->error(
    $_SERVER['REQUEST_METHOD'] . ": {$helloMessage}"
);