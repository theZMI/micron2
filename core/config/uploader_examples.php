<?php

Config(
    ['uploader', 'examples'],
    [
        'upload_path'   => BASEPATH . 'upl/examples/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'thumbs'        => [
            ['width' => 640, 'height' => 480, 'path' => BASEPATH . 'upl/examples/640x480/']
        ]
    ]
);