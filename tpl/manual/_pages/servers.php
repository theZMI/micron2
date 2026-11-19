<h1>Настройка сервера: Apache2 и NGINX</h1>

<h2>Apache2</h2>
<p>Всё нужное уже лежит в <code>.htaccess</code> в корне сайта и в папках <code>tmp</code>, <code>upl</code>, <code>apps</code>, <code>db_backups</code> - ничего распаковывать не нужно. Должны быть включены модули <code>mod_rewrite</code> и <code>mod_headers</code> и разрешено <code>AllowOverride All</code> (в docker-окружении это уже сделано).</p>
<p>Что делает корневой <code>.htaccess</code>:</p>
<ul>
    <li>все запросы, кроме исключений, отправляет на <code>/index.php?micron_query=адрес</code>;</li>
    <li>напрямую отдаёт только склеенные css/js (<code>tmp/_auto_merge_css_js</code>, <code>tmp/_admin_auto_merge_css_js</code>), qr-коды, сборку webpack (<code>i/webpack/</code>) и файлы CKEditor. Остальные файлы (картинки из <code>i/</code> и <code>upl/</code>) отдаёт сам движок через <code>BrowserDataCache</code> - с проверкой папки и типа файла и с кешированием в браузере;</li>
    <li>запрещает доступ к <code>.env</code>, <code>composer.json</code>, <code>Makefile</code>, <code>README.md</code>;</li>
    <li>выставляет заголовки безопасности и кеширование статики на год;</li>
    <li>задаёт максимальный размер загрузки (50 МБ).</li>
</ul>

<h2>NGINX + PHP-FPM</h2>
<p>Для сайта <code>site.ru</code> в папке <code>/var/www/site.ru</code> конфигурация может выглядеть так:</p>
<?= DocCode(<<<'CODE'
    server {
        listen 80;
        server_name site.ru www.site.ru;
        root /var/www/site.ru;
        index index.php;

        client_max_body_size 50m; # Иначе ошибка 413 (Request Entity Too Large)

        access_log /var/log/nginx/site.ru.access.log;
        error_log  /var/log/nginx/site.ru.error.log;

        # Скрытые файлы (.env, .htaccess, .git) и служебные файлы проекта
        location ~ /\.                                        { deny all; }
        location ~ ^/(composer\.(json|lock)|Makefile|README\.md)$ { deny all; }

        # Статика, которую можно отдавать напрямую
        location ^~ /tmp/_auto_merge_css_js/       { access_log off; expires 1y; }
        location ^~ /tmp/_admin_auto_merge_css_js/ { access_log off; expires 1y; }
        location ^~ /tmp/qrcodes/                  { access_log off; }
        location ^~ /i/webpack/dist/               { access_log off; expires 1y; }
        location ^~ /lib/CKEditor/                 { access_log off; }
        location = /favicon.ico                    { access_log off; log_not_found off; }

        # Всё остальное (страницы, robots.txt и sitemap.xml, а также картинки из i/ и upl/) - через движок
        location / {
            rewrite ^/(.*)$ /index.php?micron_query=$1 last;
        }

        # Выполняем только index.php
        location = /index.php {
            fastcgi_pass unix:/run/php/php8.2-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
        location ~ \.php$ { deny all; }
    }
    CODE, 'nginx', '/etc/nginx/sites-available/site.ru') ?>
<p>Аргументы запроса (<code>?page=2</code>) NGINX при <code>rewrite</code> добавит сам. HTTPS удобнее всего настроить через certbot.</p>
<div class="manual-note">
    <p>Если перед сайтом стоит прокси или балансировщик, впишите его IP в <code>TRUSTED_PROXIES</code> в <code>.env</code> - иначе все посетители будут с IP прокси, а защита от подбора пароля начнёт блокировать всех сразу.</p>
</div>

<h2>После выкладки</h2>
<ol>
    <li><code>composer install --no-dev</code></li>
    <li><code>cd i/webpack &amp;&amp; npm ci &amp;&amp; npm run build</code></li>
    <li>Проверить <code>.env</code>: <code>DEBUG_MODE=0</code>, <code>ENV=production</code>, свой <code>SITE_ROOT_URL</code> (с https), пароли и ключи.</li>
    <li>Права на запись - только для <code>tmp</code>, <code>upl</code>, <code>db_backups</code>.</li>
    <li>Добавить задачи cron (смотрите <a href="<?= $url('extras') ?>">Уведомления, cron и прочее</a>).</li>
    <li>Миграции применятся сами при первом запросе.</li>
</ol>
