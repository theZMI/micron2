<?php

Config(
    ['uploader', 'managers'],
    [
        'upload_path'   => BASEPATH . 'upl/managers/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'thumbs'        => [
            ['width' => 35, 'height' => 35, 'path' => BASEPATH . 'upl/managers/35x35/'],
            ['width' => 80, 'height' => 80, 'path' => BASEPATH . 'upl/managers/80x80/'],
            ['width' => 105, 'height' => 105, 'path' => BASEPATH . 'upl/managers/105x105/'],
            ['width' => 640, 'height' => 640, 'path' => BASEPATH . 'upl/managers/640x640/'],
        ]
    ]
);