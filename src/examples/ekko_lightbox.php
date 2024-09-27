<?php

if (Get('get_response')) {
    sleep(3);
    echo str_repeat('<p>' . 'Hello world: ' . date('d-m-Y H:i:s') . '</p>', 10);
    die();
}
