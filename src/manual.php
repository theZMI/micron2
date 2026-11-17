<?php

// Документация движка: /manual?p=раздел. Текст разделов - tpl/manual/_pages/{раздел}.php
$groups = [
    'Начало' => [
        'intro'         => 'Введение',
        'setup'         => 'Установка',
        'fast_start'    => 'Быстрый старт',
        'glossary'      => 'Глоссарий',
        'scheme'        => 'Ход выполнения приложения',
        'structure'     => 'Структура папок и файлов',
    ],
    'Основные элементы' => [
        'funcs'         => 'Функции, переменные и константы',
        'config'        => 'Конфигурация и .env',
        'routing'       => 'Роутинг и middleware',
        'components'    => 'Работа с компонентами',
        'langs'         => 'Мультиязычность',
        'db'            => 'Работа с моделями и БД',
        'migrations'    => 'Миграции БД',
        'assets'        => 'CSS и JS: ExtraPacker и webpack',
        'design'        => 'Оформление и темы',
        'libs'          => 'Библиотеки',
    ],
    'Готовые модули' => [
        'admin'         => 'Админка',
        'users'         => 'Пользователи, кабинет и API',
        'shop'          => 'Магазин',
        'payments'      => 'Приём оплаты',
        'extras'        => 'Уведомления, cron и прочее',
    ],
    'Сервер и качество' => [
        'debug'         => 'Отладка и обработка ошибок',
        'security'      => 'Безопасность',
        'servers'       => 'Apache2 и NGINX',
    ],
    'Учебник' => [
        'step_by_step'  => 'Создание сайта по шагам',
    ],
];

$pages = array_merge(...array_values($groups));
$page  = (string)Get('p', 'intro');
if (!isset($pages[$page])) {
    IncludeCom('404');
    ExitCom();
    return;
}

$keys  = array_keys($pages);
$index = array_search($page, $keys, true);
$prev  = $index > 0 ? $keys[$index - 1] : null;
$next  = $keys[$index + 1] ?? null;
$url   = fn(string $p) => $p === 'intro' ? SiteRoot('manual') : SiteRoot("manual&p={$p}");

$g_lang['m_title'] = $pages[$page] . ' // Документация Micron';

BreadCrumb::getInstance()->add(['text' => 'Документация', 'link' => SiteRoot('manual')]);
if ($page !== 'intro') {
    BreadCrumb::getInstance()->add(['text' => $pages[$page]]);
}
