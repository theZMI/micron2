<?php

require_once BASEPATH . 'core/func/require_once_all.php';

// Определяем все глобальные переменные
$g_config = [];
$g_lang = [];
$g_user = new stdClass();
$g_admin = new stdClass();
$g_cart = new stdClass();
$g_favorites = new stdClass();

RequireOnceAll(BASEPATH . 'core/func');

// Определяем язык под которым запущен сайт (будет доступен в константе LANG)
require_once BASEPATH . 'core/config/main.php';
require_once BASEPATH . 'core/config/languages.php';
DefineLang();

RequireOnceAll(BASEPATH . 'core/config');
RequireOnceAll(BASEPATH . 'core/init');

// Подключаем языковые файлы из автозагрузки
RequireOnceAll(BASEPATH . 'lang/' . DEF_LANG . '/_autoload');
RequireOnceAll(BASEPATH . 'lang/' . LANG . '/_autoload');
$g_lang['m_defTitle'] = L('m_title');

// Разрешаем ajax-запросы откуда угодно
Cors();
