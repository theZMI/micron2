<?php

Config(['uploader', 'default_config'], [
    // Путь до папки, куда будет загружен файл
    'upload_path'   => BASEPATH . 'upl/files/',
    // Типы MIME, описывающие типы файлов, разрешенных для загрузки. Знак * - означаем можно любой тип. Внимание! Расширение для изображений (jpg, png и др.) должны идти в конце всех остальных типов
    'allowed_types' => 'txt|zip|doc|docx|xls|pdf|rar|apk|mp4|mov|avi|bmp|gif|jpeg|jpg|png|tiff',
    // Максимальный размер файла (в килобайтах). Если ограничения нет, то пишем 0
    'max_size'      => 50000,
    // Если TRUE, и в папке уже есть файл с тем же именем, иначе к имени заливаемого файла добавится порядковый номер
    'overwrite'     => false,
    // Если TRUE, то имя файла преобразуется в случайным образом сгенерированную строку
    'encrypt_name'  => true,
    // Если TRUE, то все пробелы в имени файла будут преобразованы в знак подчеркивания
    'remove_spaces' => false,
    // Максимальная ширина картинки в пикселях. 0 — не ограниченно
    'max_width'     => 50000,
    // Максимальная высота картинки в пикселях. 0 — не ограниченно.
    'max_height'    => 50000,
    'thumbs'        => [
        // Список thumb-ов который нужно сгенерировать.
        // ['width' => 50, 'height' => 50, 'path' => BASEPATH . 'upl/files/50x50/'],
        // ['width' => 100, 'height' => 100, 'path' => BASEPATH . 'upl/files/100x100/']
    ]
]);

// Отдельная папка для сохранения аватарок пользователей
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