<?php

use \Pecee\SimpleRouter\SimpleRouter;

// Получаем список языков вида название_языка=>uri, но так, чтобы дефолтный язык был последним, иначе он перебьёт пути с языком в url-е
$getLangs = function () {
    $langs = Config('langs');
    unset($langs[DEF_LANG]);
    foreach ($langs as $k => $v) {
        $langs[$k] = "{$k}/";
    }
    $langs[DEF_LANG] = '';
    return $langs;
};

// Админка
SimpleRouter::group(['middleware' => \Middlewares\AdminAuthMiddleware::class], function () {
    SimpleRouter::all('/_admin/{end_uri}', function ($end_uri) {
        $end_uri = FileSys::filenameSecurity($end_uri);
        TryIncludeCom("_admin/{$end_uri}");
    })->where(['end_uri' => '.*?']);
});

foreach ($getLangs() as $lang => $langUri) {
    // Кабинет пользователя
    SimpleRouter::group(['middleware' => \Middlewares\UserAuthMiddleware::class], function () use ($langUri) {
        SimpleRouter::all("/{$langUri}user/{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            TryIncludeCom("/{$langUri}user/{$end_uri}");
        })->where(['end_uri' => '.*?']);
    });

    // Все запросы сайта
    SimpleRouter::group(['middleware' => \Middlewares\CommonMiddleware::class], function () use ($langUri) {
        // *-*-* Уникальные запросы сайта необходимо вставлять вот сюда: *-*-*
        // ... (такие запросы которые не подходят под стандартные правила микрона когда uri == подключаемым компонентам (src+tpl)
        // *-*-* Конец уникальных запросов сайта *-*-*

        // Стандартный тип подключения микрона (т.е. contacts/about_us -> IncludeCom('contacts/about_us'))
        SimpleRouter::all("/{$langUri}{end_uri}", function ($end_uri) use ($langUri) {
            $end_uri = FileSys::filenameSecurity($end_uri);
            if (in_array($end_uri, ['index.php', 'index.html', 'index.htm'])) {
                IncludeCom($langUri . Config('defaultComponent'));
                return;
            }
            TryIncludeCom("{$langUri}{$end_uri}");
        })->where(['end_uri' => '.*?']);
        SimpleRouter::all("/{$langUri}", function () use ($langUri) {
            TryIncludeCom($langUri . Config('defaultComponent'));
        });
    });
}