<?php

$helloMessage = "I'm alive! Right now: " . gmdate("d-m-Y H:i:s") . ' UTC';
(new ApiResponse())->normal(
    $_SERVER['REQUEST_METHOD'] . ": {$helloMessage}"
);