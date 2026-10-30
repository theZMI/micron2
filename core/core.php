<?php

BM('Request / Engine / Core');

require_once BASEPATH . 'core/func/require_once_all.php';

// Определяем все глобальные переменные
$g_config  = [];
$g_lang    = [];
$g_admin   = new stdClass();
$g_user    = new stdClass();

BM('Request / Engine / Core / Func');
RequireOnceAll(BASEPATH . 'core/func');
BM('Request / Engine / Core / Func');

BM('Request / Engine / Core / Config');
// Определяем язык под которым запущен сайт (будет доступен в константе LANG)
require_once BASEPATH . 'core/config/main.php';
require_once BASEPATH . 'core/config/languages.php';
DefineLang();

// Подключаем языковые файлы из автозагрузки
RequireOnceAll(BASEPATH . 'lang/' . DEF_LANG . '/_autoload');
RequireOnceAll(BASEPATH . 'lang/' . LANG . '/_autoload');
$g_lang['m_defaultTitle'] = L('m_title');

RequireOnceAll(BASEPATH . 'core/config');
BM('Request / Engine / Core / Config');

BM('Request / Engine / Core / Init');
// Порядок инициализации важен, поэтому подключаем файлы явно
require_once BASEPATH . 'core/init/db.php';
require_once BASEPATH . 'core/init/error_logger.php';
require_once BASEPATH . 'core/init/main.php';
require_once BASEPATH . 'core/init/www_redirecter.php';
require_once BASEPATH . 'core/init/pay_system.php';
require_once BASEPATH . 'core/init/_admin/menu.php'; // После того, как меню админки заполнено в core/config/_admin/menu
require_once BASEPATH . 'core/init/router.php';
RequireOnceAll(BASEPATH . 'core/init'); // Остальные (пользовательские) init-файлы
BM('Request / Engine / Core / Init');

BM('Request / Engine / Core / Cors');
Cors();
BM('Request / Engine / Core / Cors');

BM('Request / Engine / Core');