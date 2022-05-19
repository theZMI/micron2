<?php

/**
 * Вывод ошибки на экран и лог или только в лог в зависимости от DEBUG_MODE
 */
class MyDebug_ErrorHook_TextNotifier extends Debug_ErrorHook_TextNotifier
{
    protected function _notifyText($subject, $body)
    {
        // Подготовка сообщения ошибки
        $msg = PHP_EOL .
            "Text notification:" . PHP_EOL .
            "\tsubject: {$subject}" . PHP_EOL .
            "\t{$body}" .
            PHP_EOL;

        // Запись ошибки в лог-файл
        (FileLogger::create(Config('logErrors')['logFile']))->error($msg);

        // Вывод ошибки на экран
        if (Config('phpIni')['display_errors']) {
            echo "<pre>$msg</pre>";
        } else {
            IncludeCom('500');
        }
    }
}
