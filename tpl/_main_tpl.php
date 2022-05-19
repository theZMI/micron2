<!doctype html>
<html lang="<?= LANG ?>" dir="ltr"<?= $htmlAttrs ?>>
<head>
    <!-- Document charset -->
    <meta charset="<?= Config('charset') ?>"/>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Config('charset') ?>"/>

    <meta http-equiv="Content-Security-Policy"
          content="default-src * 'unsafe-inline' 'unsafe-eval'; script-src * 'unsafe-inline' 'unsafe-eval'; connect-src * 'unsafe-inline'; img-src * data: blob: 'unsafe-inline'; frame-src *; style-src * 'unsafe-inline';"/>

    <!-- IE render mode -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>

    <!-- SEO -->
    <title><?= L('m_title') ?></title>
    <meta name="apple-mobile-web-app-title" content="<?= L('m_shortTitle') ?>"/>
    <?php if (!empty(L('m_description'))): ?>
        <meta name="description" content="<?= L('m_description') ?>" />
    <?php endif ?>
    <?php if (!empty(L('m_keyWords'))): ?>
        <meta name="keywords" content="<?= L('m_keyWords') ?>" />
    <?php endif ?>

    <!-- Off cache for requests -->
    <meta http-equiv="Cache-Control" content="no-cache" />

    <!-- View at mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui, min-width=320" />
    <meta name="MobileOptimized" content="320" />
    <meta name="HandheldFriendly" content="true" />

    <!-- Favicon -->
    <link rel="icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon" />
    <link rel="shortcut icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon" />

    <!-- Application icon for IOS dashboard -->
    <link rel="apple-touch-icon" href="<?= Root('i/image/touch_icon/180x180.png') ?>" />

    <!-- Clear fonts for windows -->
    <meta http-equiv="cleartype" content="on" />

    <?php IncludeCom('_dev/startup_as_mobile_app') ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu&family=Ubuntu+Condensed&display=swap" rel="stylesheet">

    <!-- Либы и код собранные через webpack -->
    <?php foreach ($cssFiles as $file): ?>
        <link rel="stylesheet" type="text/css" href="<?= Root("{$file}&mtime=" . filemtime(BASEPATH . $file)) ?>" />
    <?php endforeach; ?>

    <?php IncludeCom('_seo') ?>
</head>
<body>
    <?php IncludeCom('_main_tpl/menu') ?>

    <div id="wrapper">
        <div id="content">
            <?= $content ?>
        </div>
    </div>

    <?php IncludeCom('_main_tpl/footer') ?>

    <?php foreach ($jsFiles as $file): ?>
        <script type="text/javascript" src="<?= Root("{$file}&mtime=" . filemtime(BASEPATH . $file)) ?>"></script>
    <?php endforeach; ?>
</body>
</html>