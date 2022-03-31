<?php

function IsValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function PhoneFilter($phone)
{
    $ret = preg_replace("~[^\+|^0-9]~is", '', $phone);
    return str_replace("+8", "+7", $ret);
}

function IsValidPhone($phone)
{
    $phone1         = PhoneFilter($phone);
    $phone2         = strtr($phone, [' ' => '', '(' => '', ')' => '', '-' => '']);
    $phone3         = str_replace('+', '', $phone2);
    $lenMobilePhone = 11;

    $isValid = true;
    // Если есть "+" но не в начале
    if (strpos($phone, '+') !== false) {
        if (strpos($phone, '+') !== 0) {
            $isValid = false;
        }
    }
    // Если есть неправильная последовательность разрешённых символов
    if (
        strpos($phone, '--') !== false
        || strpos($phone, '+-') !== false
        || strpos($phone, '-+') !== false
        || strpos($phone, '((') !== false
        || strpos($phone, '))') !== false
        || strpos($phone, '()') !== false
    ) {
        $isValid = false;
    }
    // Если есть не только спец. символы и цифры
    if (strlen($phone1) != strlen($phone2)) {
        $isValid = false;
    }
    // Если длинна телефона слишком маленькая (не мобильный)
    if (strlen($phone3) != $lenMobilePhone) {
        $isValid = false;
    }

    return $isValid;
}

function IsValidUrl($url)
{
    $regExp = "~
                    ^(?:(?:http)s?://)?
                    (?:www\.)?
                    (?:[-\w]{1,256}\.)+
                    (?:
                        (?:[-\w]{2,32})
                        /?
                    )+$
               ~uxis";
    preg_match($regExp, $url, $m);

    return !empty($m);
}
