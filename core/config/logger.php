<?php

Config('useDebugErrorHook', true); // Использовать ли DebugErrorHook для перехвата ошибок
Config('stopIfError', true); // Останавливать ли на некритичных ошибках? (Работает только если DebugErrorHook включён)
Config('logErrors', [
    'repeatTmp' => BASEPATH . 'tmp/log/unRepeater', // Просто папка для контроля одинаковых ошибок (что бы emailTimeRepeat работал)
    'logFile' => BASEPATH . 'tmp/log/php_errors.log', // Файл для сохранения лога ошибок (имеет смысл если используется MyDebug_ErrorHook_TextNotifier в classNotifier или свой класс обработки ошибок)
    'emailTimeRepeat' => 3 * 60, // Не слать одинаковые сообщения об ошибках чаще чем раз в 3 минуты
    'email' => 'zmigpost@gmail.com', // На этот адрес будут присылаться сообщения об ошибках
    'classNotifier' => 'MyDebug_ErrorHook_Notifier' // Ошибки логгируются в БД
    // 'MyDebug_ErrorHook_TextNotifier' // Ошибки логгируются в текстовый файл
]);
