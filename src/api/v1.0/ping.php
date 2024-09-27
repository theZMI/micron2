<?php

$helloMessage = "I'm live. Now: " . gmdate("d-m-Y H:i:s") . ' UTC';
(new ApiResponse())->normal(
    $_SERVER['REQUEST_METHOD'] . ": {$helloMessage}"
);