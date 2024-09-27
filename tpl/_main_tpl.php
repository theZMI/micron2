<!doctype html>
<html lang="<?= LANG ?>" dir="ltr"<?= $htmlAttrs ?>>
<head>
    <?php IncludeCom('_common_head') ?>

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
<body>
<div id="wrapper">
    <?php IncludeCom('_main_tpl/menu') ?>
    <?= $content ?>
</div>
<?php IncludeCom('_main_tpl/footer') ?>
</body>
</html>