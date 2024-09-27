<!DOCTYPE html>
<html lang="<?= LANG ?>" dir="ltr" id="is-admin-area">
<head>
    <?php IncludeCom('_common_head') ?>
    <title>Административный раздел</title>

    <!-- Не индексируем роботами (хотя они и так войти не должны) -->
    <meta name="robots" content="noindex, nofollow">

    <!-- Масштаб отображения сайта на мобильных устройствах -->
    <meta name="viewport" content="width=1200"/>
    <meta name="MobileOptimized" content="1200"/>
    <meta name="HandheldFriendly" content="true"/>

    <!-- Краткий title для иконки iOS вместо обычного title сайта -->
    <meta name="apple-mobile-web-app-title" content="Admin area"/>

    <link rel="stylesheet" type="text/css" href="<?= Root('i/css/_admin/main.less') ?>"/>
    <script type="text/javascript" src="<?= Root('i/js/_admin/main.js') ?>"></script>
</head>
<body>
<div id="wrapper">
    <div id="admin-menu">
        <?php IncludeCom('_dev/menu', ['logo' => $logo, 'menu' => Config('admin_menu'), 'rightMenu' => Config('admin_menu_right'), 'w100' => true]) ?>
    </div>
    <?= $content ?>
</div>
<?php if (IS_ADMIN_AUTH): ?>
    <div id="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="pt-4 pb-4">
                        Вы авторизованы как: <strong><?= $g_admin->login ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
</body>
</html>