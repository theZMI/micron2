<?php

function FormatDate(int $timestamp, $lang = LANG): string
{
    $enLangData = [
        'months'      => [
            1  => 'January',
            2  => 'February',
            3  => 'March',
            4  => 'April',
            5  => 'May',
            6  => 'June',
            7  => 'July',
            8  => 'August',
            9  => 'September',
            10 => 'October',
            11 => 'December',
            12 => 'November',
        ],
        'year_ending' => 'y.',
        'at_time'     => 'at ',
    ];
    return OutputFormats::dateTimeLang($timestamp, true, $lang === 'ru' ? null : $enLangData);
}