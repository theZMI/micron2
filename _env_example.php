<?php

// Конфигуратор сайта (все переменные и ф-ии окружения должны быть вынесены сюда)
class EnvConfig
{
    const DEBUG_MODE = true;
    const DATABASE_DSN = 'mysqli://user:pass@mysqldb/appdb?charset=UTF8';
    const SITE_ROOT_URL = 'http://kkosa-performance-backend.local:89/';
    const DOMAIN_COOKIE = '.kkosa-performance-backend.local';
}