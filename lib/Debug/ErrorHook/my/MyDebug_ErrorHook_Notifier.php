<?php

require_once BASEPATH . 'model/ErrorLoggerModel.php';

// Подключаю языковые данные т.к. шаблон вывода ошибки использует их
RequireOnceAll(BASEPATH . 'lang/' . DEF_LANG . '/_autoload');

/**
 * Вывод ошибки на экран и лог или только в лог в зависимости от DEBUG_MODE
 */
class MyDebug_ErrorHook_Notifier implements Debug_ErrorHook_INotifier
{
    public function notify($errno, $errstr, $errfile, $errline, $trace)
    {
        global $g_lang, $g_user;

        $displayErrors = ini_get('display_errors');

        $needStop = in_array($errno, ['E_WARNING', 'E_NOTICE', 'E_USER_WARNING', 'E_USER_NOTICE']) ? Config('stopIfError') : $errno;
        $browser = new Browser();
        $logger = new ErrorLoggerModel();

        $logger->create_time = time();
        $logger->_get = serialize($_GET);
        $logger->_post = serialize($_POST);
        $logger->_cookie         = serialize($_COOKIE);
        $logger->_session        = isset($_SESSION) ? serialize($_SESSION) : '';
        $logger->_server         = serialize($_SERVER);
        $logger->_files          = isset($_FILES) ? serialize($_FILES) : '';
        $logger->backtrace       = Debug_ErrorHook_Util::backtraceToString($trace);
        $logger->sql             = MyDataBaseLog::render();
        $logger->ip              = ErrorLoggerModel::ipAddress();
        $logger->browser         = $browser->getBrowser();
        $logger->browser_version = $browser->getVersion();
        $logger->platform        = $browser->getPlatform();
        $logger->aol = $browser->isAol() ? $browser->getAolVersion() : '';
        $logger->g_config = serialize(Config());
        $logger->g_lang = serialize($g_lang);
        $logger->g_user          = method_exists($g_user, 'getData') ? json_encode($g_user->getData()) : null;
        $logger->errno           = $errno;
        $logger->errstr          = $errstr;
        $logger->errfile         = $errfile;
        $logger->errline         = $errline;

        // Вывод ошибки на экран
        ob_start();
            IncludeCom($displayErrors ? '_dev/error_logger_show' : '500', ['logger' => $logger]);
        $content = ob_get_clean();

        if ($needStop) {
            @ob_clean();
            IncludeCom('_dev/main_tpl_for_stop_info', ['content' => $content, 'title' => '500 | Site error']);
            exit();
        }

        return true;
    }
}
