<?php IncludeCom('_dev/syntaxhighlighter'); ?>

<h1>Правильная переадресация</h1>
[code=Php]
// Просто перенаправляем:
UrlRedirect::go(SiteRoot('user/login'));

// Перенаправляем со статусом:
UrlRedirect::go(SiteRoot('user/login'), 403);
[/code]