<?php IncludeCom('_dev/syntaxhighlighter'); ?>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1 class="mt-4 mb-4">Загрузка файлов с сервера с проверками безопасности<br>(файл `core/config/downloader.php`)</h1>
        </div>
        <div class="col-6">
            <p>Вызов ф-ии ForceDownload должен быть до вывода любого контента. После вызова скрипт будет остановлен так как файл будет отдан в stdout</p>
            [code=Php]
            echo ForceDownload(BASEPATH . 'i/image/logo.png', 'micron_logo.png');
            [/code]
        </div>
        <div class="col-6">
            <a href="<?= GetCurUrl('a=download') ?>" class="btn btn-primary ps-4 pe-4 rounded-pill">Загрузить лого</a>
        </div>
    </div>
</div>