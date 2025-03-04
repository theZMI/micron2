<?php

// Если включён use_error_logger, то мы логируем ошибки в БД и файл
// В БД через модель ErrorLoggerModel при включённом в конфиге log_errors_into_db
// В файл который указан в php.ini->error_log и при включённом php.ini->log_errors (эти параметры задаются в core/config/php_ini.php
Config('use_error_logger', true); // Использовать ли собственный улавливатель ошибок
Config('log_errors_into_db', Config('use_error_logger'));