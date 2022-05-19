<?php

// Пытаемся найти путь для переданного URL (либо провести роутинг, либо отправить запрос на скачку файла, либо выдать в поток вывода картинку и пр.) и если окажется просто вызов скрипта то пройдём дальше по коду
EngineQueryManager::tryAllBranches(GetQuery());

// Продолжится код только если это скрипт (если же файл или перенаправление то в EngineQueryManager::tryAllBranches уже будет exit)
BrowserDataCache::notCache();

new Php(); // Настройка php и включение профилирования ошибок
header('Content-type: text/html; charset=' . Config('charset'));
