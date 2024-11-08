<?php

function HasHtml($content)
{
    if (empty($content)) {
        return false;
    }
    return strlen($content) != strlen(strip_tags($content));
}

// Переводит все ссылки <a href='...'>...</a> в просто написанный текст
function HrefToText($str)
{
    $pattern1 = "~<\s*a\s*href\s*=\s*\"(.*?)\".*?>.*?<\s*\/\s*a\s*>~";
    $pattern2 = "~<\s*a\s*href\s*=\s*'(.*?)'.*?>.*?<\s*\/\s*a\s*>~";
    $pattern3 = "~<\s*a\s*href\s*=\s*([^>^\s]*).*?>.*?<\s*\/\s*a\s*>~";

    $str = preg_replace($pattern1, "$1", $str);
    $str = preg_replace($pattern2, "$1", $str);
    $str = preg_replace($pattern3, "$1", $str);

    return $str;
}

// Переводит написанные текстом ссылки в href-ы
function TextToHref($str)
{
    $urlPattern1 = '~(?<=\s|\xA0|^)((?:http|https|ftp|mailto):(?:(?:[A-Za-z0-9$_.+!*(),;/?:@&\~=-])|%[A-Fa-f0-9]{2}){2,}(?:#(?:[a-zA-Z0-9][a-zA-Z0-9$_.+!*(),;/?:@&\~=%-]*))?(?:[A-Za-z0-9$_+!*();/?:\~-]))~im';
    $urlPattern2 = '~(?<=\s|^)(www\.[A-Za-z0-9$_.+!*(),;/?:@&\~=-]{2,}\.(?:(?:com)|(?:co)|(?:uk)|(?:org))(?:(?:[a-zA-Z0-9#$_.+!*(),;/?:@&\~=%-]*))?)~';

    $str = preg_replace($urlPattern1, "<a href=\"$1\">$1</a>", $str);
    $str = preg_replace($urlPattern2, "<a href=\"http://$1\">$1</a>", $str);

    return $str;
}

// Функция перевода текста с кириллицы в транскрипт
function Translit($str)
{
    $tr = [
        "А" => "A",
        "Б" => "B",
        "В" => "V",
        "Г" => "G",
        "Д" => "D",
        "Е" => "E",
        "Ё" => "E",
        "Ж" => "J",
        "З" => "Z",
        "И" => "I",
        "Й" => "Y",
        "К" => "K",
        "Л" => "L",
        "М" => "M",
        "Н" => "N",
        "О" => "O",
        "П" => "P",
        "Р" => "R",
        "С" => "S",
        "Т" => "T",
        "У" => "U",
        "Ф" => "F",
        "Х" => "H",
        "Ц" => "TS",
        "Ч" => "CH",
        "Ш" => "SH",
        "Щ" => "SCH",
        "Ъ" => "",
        "Ы" => "YI",
        "Ь" => "",
        "Э" => "E",
        "Ю" => "YU",
        "Я" => "YA",
        "а" => "a",
        "б" => "b",
        "в" => "v",
        "г" => "g",
        "д" => "d",
        "е" => "e",
        "ё" => "e",
        "ж" => "j",
        "з" => "z",
        "и" => "i",
        "й" => "y",
        "к" => "k",
        "л" => "l",
        "м" => "m",
        "н" => "n",
        "о" => "o",
        "п" => "p",
        "р" => "r",
        "с" => "s",
        "т" => "t",
        "у" => "u",
        "ф" => "f",
        "х" => "h",
        "ц" => "ts",
        "ч" => "ch",
        "ш" => "sh",
        "щ" => "sch",
        "ъ" => "y",
        "ы" => "yi",
        "ь" => "",
        "э" => "e",
        "ю" => "yu",
        "я" => "ya"
    ];
    return strtr($str, $tr);
}

// Переводит текст в валидный URL
function TextToUrl($str, $separator = '-')
{
    $str         = Translit($str);
    $q_separator = preg_quote($separator);
    $trans       = [
        '&.+?;'                   => '',
        '[^a-z0-9 _-]'            => '',
        '\s+'                     => $separator,
        '(' . $q_separator . ')+' => $separator
    ];
    $str         = strip_tags($str);

    foreach ($trans as $key => $val) {
        $str = preg_replace("#" . $key . "#i", $val, $str);
    }
    return trim(strtolower($str), $separator);
}

// Обрежет текст в указанную длину (при возможности по разделителю, чтобы сохранить слово)
function TextWithMaxLen($str, $n = 100)
{
    $end_char = '&#8230;';
    if (strlen($str) < $n) {
        return $str;
    }

    $str = str_replace(array("\r\n", "\r", "\n"), ' ', $str);
    $str = preg_replace("/\s+/", ' ', $str);

    if (strlen($str) <= $n) {
        return $str;
    }

    $out = '';
    foreach (explode(' ', trim($str)) as $val) {
        $out .= $val . ' ';

        if (strlen($out) >= $n) {
            $out = trim($out);
            return (strlen($out) == strlen($str)) ? $out : $out . $end_char;
        }
    }

    return $out;
}