<h1 class="mt-4 mb-4">Engine:</h1>

<h3 class="mt-2 mb-2">Functions:</h3>
<p>_StrReplaceFirst</p>
<p>IncludeCom</p>
<p>TryIncludeCom - попробует подключить компонент, но если его нет, то подключит 404-ую</p>
<p>ExitCom</p>
<p>GetQuery</p>
<p>GetCurUrl</p>
<p>Root</p>
<p>SiteRoot</p>
<p>Get</p>
<p>Post</p>
<p>L - получить строку из переводов (/lang/ЯЗЫК/) или вернём переданный ключ. К примеру вызываем: echo L('Hello world'); - тогдf если в файлах lang/{ru|en|...} есть перевод, то вернём его, а если нет, то строчку: Hello world</p>
<p>Config - чтение, запись в g_config</p>
<p>GetDefaultDb - получение ссылки на объект DbSimple с дефолтной БД (удобно использовать в моделях)</p>
<p>Xmp - распечатка переменной, массива, объекта и пр.</p>
<p>ToLog - запись в лог</p>
<p>VarDump</p>
<p>HasHtml</p>
<p>Translit</p>
<p>ForceDownload - отдаст в браузер файл на скачку</p>
<p>FormatDate - вывод даты в красивом формате</p>
<p>FormatTimeInterval - вывод времени в красивом формате</p>
<p>GetLocationByIP</p>
<p>GetCountries</p>
<p>GetCountryByCode</p>
<p>GetCoordinatesByAddress - плохо работает, но в целом возможно получить координаты по адресу</p>
<p>GetClientIP</p>
<p>Msg - сообщение в html-рамочке</p>
<p>MsgOk - сообщение об успехе</p>
<p>MsgErr - сообщение об ошибке</p>
<p>MsgWarning</p>
<p>SendMail - отправка письма через PhpMailer (подключение настраивается в .env и потом в core/config/send_mail.php)</p>
<p>IsValidEmail</p>
<p>IsValidPhone</p>
<p>IsValidUrl</p>
<p>PhoneFilter</p>
<p>MemoryHas - проверить наличие чего в кеше по ключу</p>
<p>MemoryGet - получить из кеша</p>
<p>MemorySet - положить в кеш</p>

<h3 class="mt-2 mb-2">Библиотеки:</h3>
<p>Php - для вывода статусов и начальной настройки php</p>
<p>DbSimple - для работы с БД</p>
<p>ModelEx - для создания моделей (наследуйте свои модели от этого класса, ограничениями является: ключевое поле называется id, должно быть поле create_time типа INT, табличка должна быть в главной БД которую возвращает ф-я GetDefaultDb()), если это не устраивает то смотрите классы ModelOptimized и Model</p>
<p>Uploader - для загрузки файлов</p>
<p>UrlRedirect - для редиректов (можно и через header, но класс-надстройка позволяет централизованно что-то переделать или логгировать)</p>
<p>OutputFormats - вывод данных в формате: телефона, даты, числа, суммы и др.</p>
<p>FileSys - работа с файловой системой (рекурсивное создание папок, удаление, запись файлов и пр)</p>
<p>FileLogger - работа с лог-файлами с защитой от того, что они статус слишком большими</p>
<p>SessionManager - работа с сессией</p>

<h3 class="mt-2 mb-2">Компоненты:</h3>
<p>Главный шаблон</p>
<p>Админка:
    - вход
    - текстовые страницы
    - статьи
    - лог ошибок
</p>
<p>Пользовательский кабинет</p>

<h3 class="mt-2 mb-2">_dev - компоненты:</h3>
<p>/_seo - для всяких счётчиков и метрик которые нужны только на проде</p>
<p>/_status_page - страница для отображения всяких ошибок (404, 403 и др.)</p>
<p>403, 404</p>
<p>paginator</p>
<p>autosize inputs</p>
<p>no_responsive</p>
<p>pwd_shower</p>