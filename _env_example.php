<?php

// Режим работы сайта
define('DEBUG_MODE', true);

// Конфигуратор сайта (все переменные и ф-ии окружения должны быть вынесены сюда)
class AppConfig
{
    const DATABASE_DSN  = 'mysqli://user:pass@mariadb/appdb?charset=UTF8';
    const SITE_ROOT_URL = 'http://localhost:81/';
    const DOMAIN_COOKIE = '.';
}