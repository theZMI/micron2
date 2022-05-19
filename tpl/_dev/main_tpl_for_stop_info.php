<!DOCTYPE html>
<html lang="<?= LANG ?>" dir="ltr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Config('charset') ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?></title>
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    <style>
        #wrapper {
            padding: 30px;
            position: relative;
            text-align: center;
        }

        .header-icon {
            font-size: 42px;
            padding: 15px;
        }

        .table {
            margin-bottom: 0;
        }

        .debug-panel-wrapper {
            position: relative;
        }

        .backtrace-wrapper {
            display: none;
        }
    </style>
</head>
<body>
    <div id="wrapper">
        <?= $content ?>
    </div>
</body>
</html>
