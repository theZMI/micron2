<?php

// If debug is true, then we show errors into browser
define('DEBUG_MODE', true);

// All environment variables must be here
class AppConfig
{
    const DATABASE_DSN  = 'mysqli://user:pass@mariadb/appdb?charset=UTF8';
    const SITE_ROOT_URL = 'http://localhost:81/';
    const DOMAIN_COOKIE = '.';
}