<?php

require_once BASEPATH . 'core/init/db.php';

EngineQueryManager::tryOutStaticData(GetQuery()); // Попробуем вывести картинку/asset по запрошенному url-у, если не получилось, то у нас будет выводиться страница

BrowserDataCache::offCachePage();

new Php(); // Настройка php и включение профилирования ошибок

header('Content-type: text/html; charset=' . Config('charset'));