<?php

require_once BASEPATH . 'core/func/require_once_all.php';

// Определяем все глобальные переменные
$g_config = [];
$g_lang   = [];
$g_user   = new stdClass();
$g_admin  = new stdClass();

RequireOnceAll(BASEPATH . 'core/func');

// Определяем язык под которым запущен сайт (будет доступен в константе LANG)
require_once BASEPATH . 'core/config/main.php';
require_once BASEPATH . 'core/config/languages.php';
DefineLang();

// Подключаем языковые файлы из автозагрузки
RequireOnceAll(BASEPATH . 'lang/' . DEF_LANG . '/_autoload');
RequireOnceAll(BASEPATH . 'lang/' . LANG . '/_autoload');
$g_lang['m_defaultTitle'] = L('m_title');

RequireOnceAll(BASEPATH . 'core/config');
RequireOnceAll(BASEPATH . 'core/init');