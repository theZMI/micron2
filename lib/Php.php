<?php

class Php
{
    private static $codes = [
        '200' => 'OK',
        '201' => 'Created',
        '202' => 'Accepted',
        '203' => 'Non-Authoritative Information',
        '204' => 'No Content',
        '205' => 'Reset Content',
        '206' => 'Partial Content',

        '300' => 'Multiple Choices',
        '301' => 'Moved Permanently',
        '302' => 'Found',
        '304' => 'Not Modified',
        '305' => 'Use Proxy',
        '307' => 'Temporary Redirect',

        '400' => 'Bad Request',
        '401' => 'Unauthorized',
        '403' => 'Forbidden',
        '404' => 'Not Found',
        '405' => 'Method Not Allowed',
        '406' => 'Not Acceptable',
        '407' => 'Proxy Authentication Required',
        '408' => 'Request Timeout',
        '409' => 'Conflict',
        '410' => 'Gone',
        '411' => 'Length Required',
        '412' => 'Precondition Failed',
        '413' => 'Request Entity Too Large',
        '414' => 'Request-URI Too Long',
        '415' => 'Unsupported Media Type',
        '416' => 'Requested Range Not Satisfiable',
        '417' => 'Expectation Failed',

        '500' => 'Internal Server Error',
        '501' => 'Not Implemented',
        '502' => 'Bad Gateway',
        '503' => 'Service Unavailable',
        '504' => 'Gateway Timeout',
        '505' => 'HTTP Version Not Supported',
    ];

    /**
     * Вернет правильный статус для отсылки его по header
     */
    public static function status($code)
    {
        if (empty($code) || !is_numeric($code) || !isset(self::$codes[$code])) {
            return false;
        }

        $text     = self::$codes[$code];
        $protocol = $_SERVER['SERVER_PROTOCOL'] ?? false;

        if (substr(php_sapi_name(), 0, 3) == 'cgi') {
            $ret = "Status: {$code} {$text}";
        } elseif (in_array($protocol, ['HTTP/1.1', 'HTTP/1.0'])) {
            $ret = "{$protocol} {$code} {$text}";
        } else {
            $ret = "HTTP/1.1 {$code} {$text}";
        }

        return $ret;
    }

    public function __construct()
    {
        $ini = Config('php_ini');
        array_walk(
            $ini,
            fn($v, $k) => ini_set($k, $v)
        );

        setlocale(LC_ALL, "en_US.UTF-8", "English");
        date_default_timezone_set(DEFAULT_TIME_ZONE);

        if (function_exists('mb_internal_encoding') && function_exists('mb_regex_encoding')) {
            mb_internal_encoding(Config('charset'));
            mb_regex_encoding(Config('charset'));
        }
    }
}
