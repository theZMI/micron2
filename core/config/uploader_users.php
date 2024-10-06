<?php

Config(
    ['uploader', 'users'],
    [
        'upload_path'   => BASEPATH . 'upl/users/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'thumbs'        => [
            ['width' => 35, 'height' => 35, 'path' => BASEPATH . 'upl/users/35x35/'],
            ['width' => 80, 'height' => 80, 'path' => BASEPATH . 'upl/users/80x80/'],
            ['width' => 105, 'height' => 105, 'path' => BASEPATH . 'upl/users/105x105/'],
            ['width' => 640, 'height' => 640, 'path' => BASEPATH . 'upl/users/640x640/'],
        ]
    ]
);