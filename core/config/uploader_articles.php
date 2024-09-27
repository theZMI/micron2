<?php

Config(
    ['uploader', 'articles'],
    [
        'upload_path'   => BASEPATH . 'upl/articles/',
        'allowed_types' => 'gif|jpeg|jpg|png',
        'thumbs'        => [
            ['width' => 640, 'height' => 360, 'path' => BASEPATH . 'upl/articles/640x360/'],
            ['width' => 1280, 'height' => 720, 'path' => BASEPATH . 'upl/articles/1280x720/'],
        ]
    ]
);