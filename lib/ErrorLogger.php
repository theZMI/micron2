<?php

class ErrorLogger
{
    public function __construct($func = null)
    {
        $backtraceToString = function ($backtrace) {
            // Iterate backtrace
            $calls = [];
            if (empty($backtrace)) {
                return '';
            }
            foreach ($backtrace as $i => $call) {
                if (!isset($call['file'])) {
                    $call['file'] = '(null)';
                }
                if (!isset($call['line'])) {
                    $call['line'] = '0';
                }
                $location = $call['file'] . ':' . $call['line'];
                $function = (isset($call['class'])) ?
                    $call['class'] . ($call['type'] ?? '.') . $call['function'] :
                    $call['function'];

                $params = '';
                if (isset($call['args']) && is_array($call['args'])) {
                    $args = array();
                    foreach ($call['args'] as $arg) {
                        if (is_array($arg)) {
                            $args[] = "Array(...)";
                        } elseif (is_object($arg)) {
                            $args[] = get_class($arg);
                        } else {
                            $args[] = $arg;
                        }
                    }
                    $params = implode(', ', $args);
                }

                $calls[] = sprintf('#%d  %s(%s) called at [%s]',
                    $i,
                    $function,
                    $params,
                    $location);
            }

            return implode("\n", $calls) . "\n";
        };
        $logInDb           = function ($errno, $errstr, $errfile, $errline, $trace) use ($backtraceToString) {
            global $g_user, $g_lang;

            $logger  = new ErrorLoggerModel();
            $browser = new Browser();

            $logger->create_time     = time();
            $logger->_get            = json_encode($_GET);
            $logger->_post           = json_encode($_POST);
            $logger->_cookie         = json_encode($_COOKIE);
            $logger->_session        = isset($_SESSION) ? json_encode($_SESSION) : '';
            $logger->_server         = json_encode($_SERVER);
            $logger->_files          = isset($_FILES) ? json_encode($_FILES) : '';
            $logger->backtrace       = $backtraceToString($trace);
            $logger->sql             = json_encode(MyDataBaseLog::$dbLog);
            $logger->ip              = GetClientIP();
            $logger->browser         = $browser->getBrowser();
            $logger->browser_version = $browser->getVersion();
            $logger->platform        = $browser->getPlatform();
            $logger->g_config        = json_encode(Config());
            $logger->g_lang          = json_encode($g_lang);
            $logger->g_user          = method_exists($g_user, 'getData') ? json_encode($g_user->getData()) : null;
            $logger->errno           = $errno;
            $logger->errstr          = $errstr;
            $logger->errfile         = $errfile;
            $logger->errline         = $errline;

            return $logger;
        };
        $logInFile         = function ($errno, $errstr, $errfile, $errline, $trace) use ($backtraceToString) {
            FileLogger::create(ini_get('error_log'))->error(
                "Message: $errstr($errno)\n" .
                "File: $errfile($errline)\n" .
                "Backtrace: " . $backtraceToString($trace)
            );
            return true;
        };
        $errorHandler      = $func ?: function ($errno, $errstr, $errfile, $errline, $trace) use ($logInDb, $logInFile) {
            $dbLogger = null;
            if (Config('logErrorsIntoDb')) {
                $dbLogger = $logInDb($errno, $errstr, $errfile, $errline, $trace);
            }

            $isWriteIntoLogFile = false;
            if (ini_get('log_errors')) {
                $isWriteIntoLogFile = $logInFile($errno, $errstr, $errfile, $errline, $trace);
            }

            $logger = $dbLogger;
            if (!$logger) {
                $logger = (object)[
                    'id'        => 'No id',
                    'errstr'    => $errstr,
                    'errno'     => $errno,
                    'errfile'   => $errfile,
                    'errline'   => $errline,
                    'backtrace' => 'Wrote into DB: ' . ($dbLogger ? 'yes' : 'no') . PHP_EOL .
                                   'Wrote into File: ' . ($isWriteIntoLogFile ? 'yes' : 'no'),
                ];
            }
            ob_start();
            IncludeCom(ini_get('display_errors') ? '_dev/error_logger_show' : '500', ['logger' => $logger]);
            $infoAboutError = ob_get_clean();

            IncludeCom('_dev/main_tpl_for_stop_info', ['content' => $infoAboutError, 'title' => '500 | Site error :-(']);
            die;
        };

        set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($errorHandler) {
            if (!($errno & error_reporting())) {
                return;
            }
            $trace = debug_backtrace();
            array_shift($trace);
            $errorHandler($errno, $errstr, $errfile, $errline, $trace);
        });
        register_shutdown_function(function () use ($errorHandler) {
            $error = error_get_last();
            if (!is_array($error) || !in_array($error['type'], array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR))) {
                return;
            }
            $errorHandler($error['type'], $error['message'], $error['file'], $error['line'], null);
        });
    }
}