<!doctype html>
<html lang="<?= LANG ?>" dir="ltr"<?= $htmlAttrs ?>>
<head>
    <?php IncludeCom('_common_head') ?>
    <?php IncludeCom('_dev/syntaxhighlighter') ?>

    <!-- SEO -->
    <title><?= L('m_title') ?></title>
    <meta name="apple-mobile-web-app-title" content="<?= L('m_shortTitle') ?>"/>
    <?php if (!empty(L('m_description'))): ?>
        <meta name="description" content="<?= L('m_description') ?>"/>
    <?php endif ?>
    <?php if (!empty(L('m_keyWords'))): ?>
        <meta name="keywords" content="<?= L('m_keyWords') ?>"/>
    <?php endif ?>

    <!-- View at mobile devices -->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, min-width=400"/>
    <meta name="MobileOptimized" content="400"/>
    <meta name="HandheldFriendly" content="true"/>

    <?php IncludeCom('_seo') ?>
</head>
<body class="d-flex flex-column">
<div id="wrapper" class="d-flex flex-column flex-grow-1">
    <?php IncludeCom('_main_tpl/menu') ?>
    <div class="container d-flex flex-column flex-grow-1">
        <div class="row flex-grow-1">
            <div class="col d-flex flex-column flex-grow-1">
                <?= $content ?>
            </div>
        </div>
    </div>
</div>
<?php IncludeCom('_main_tpl/footer') ?>
</body>
</html>