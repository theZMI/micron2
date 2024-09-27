<?php

// Если включён useErrorLogger, то мы логируем ошибки в БД и файл
// В БД через модель ErrorLoggerModel при включённом в конфиге logErrorsIntoDb
// В файл который указан в php.ini->error_log и при включённом php.ini->log_errors (эти параметры задаются в core/config/php_ini.php
Config('useErrorLogger', true); // Использовать ли собственный улавливатель ошибок
Config('logErrorsIntoDb', Config('useErrorLogger'));