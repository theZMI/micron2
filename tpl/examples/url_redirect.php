<h1 class="mt-4 mb-4">Правильная переадресация</h1>
[code=Php]
// Просто перенаправляем:
UrlRedirect::go(SiteRoot('user/login'));

// Перенаправляем со статусом:
UrlRedirect::go(SiteRoot('user/login'), 403);
[/code]