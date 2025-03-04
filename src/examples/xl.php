<?php

$downloadAsXL = Get('a') === 'download';
$readLocalXL = Get('a') === 'read';

if ($downloadAsXL) {
    $data = [
        ['Поле 1', 'Поле 2', 'Поле 3', 'Поле 4', 'Поле 5'],
        ['a' => '1', 'b' => '2', 'c' => '3', 'd' => '4', 'e' => '5'],
        ['a' => 11, 'b' => 22, 'c' => 33, 'd' => 44, 'e' => 55],
        ['a' => '01-01-2025', 'b' => '01-01-2025 12:00:00', 'c' => time(), 'd' => '2025-01-01', 'e' => '2025/01/01'],
    ];
    DownloadAsXL($data, 'micron_example_xl_file.xls');
} elseif ($readLocalXL) {
    $data = ReadXL(BASEPATH . 'i/data/example_xl_file.xls', [12, 13]);
    __($data);
}