<!-- Document charset -->
<meta charset="<?= Config('charset') ?>"/>
<meta http-equiv="Content-Type" content="text/html; charset=<?= Config('charset') ?>"/>

<meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline' 'unsafe-eval'; script-src * 'unsafe-inline' 'unsafe-eval'; connect-src * 'unsafe-inline'; img-src * data: blob: 'unsafe-inline'; frame-src *; style-src * 'unsafe-inline';"/>

<!-- IE render mode -->
<meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1"/>

<!-- Off cache for requests -->
<meta http-equiv="Cache-Control" content="no-cache"/>

<!-- Clear fonts for windows -->
<meta http-equiv="cleartype" content="on"/>

<!-- Favicon -->
<link rel="icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>
<link rel="shortcut icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>

<!-- Application icon for IOS dashboard -->
<link rel="apple-touch-icon" href="<?= Root('i/image/touch_icon/180x180.png') ?>"/>

<!-- Place to inject js/css from webpack -->
<?php foreach (Config(['webpack', 'cssFiles']) as $file): ?>
    <link rel="stylesheet" type="text/css" href="<?= Root("{$file}&mtime=" . filemtime(BASEPATH . $file)) ?>"/>
<?php endforeach ?>
<?php foreach (Config(['webpack', 'jsFiles']) as $file): ?>
    <script type="text/javascript" src="<?= Root("{$file}&mtime=" . filemtime(BASEPATH . $file)) ?>"></script>
<?php endforeach ?>

<!-- Place to inject js/css from extraPacker -->
<!-- extraPackerJs -->
<!-- extraPackerCss -->

<?php IncludeCom('_dev/ekko_lightbox') ?>
<?php IncludeCom('_dev/jcrop') ?>
<?php IncludeCom('_dev/jqueryui') ?>
