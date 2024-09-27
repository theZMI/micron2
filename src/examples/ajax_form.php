<?php

$type = Get('type');

if (+Get('get_response')) {
    if ($type === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode([
            'is_success' => true,
            'get_data'   => $_POST,
            'response'   => 'Hello world: ' . date('d-m-Y H:i:s')
        ]));
    }

    die('Hello world: ' . date('d-m-Y H:i:s'));
}
