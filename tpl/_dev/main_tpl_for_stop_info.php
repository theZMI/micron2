<!DOCTYPE html>
<html lang="<?= LANG ?>" dir="ltr">
<head class="is-stop-by-error-template">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Config('charset') ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $title ?></title>
    <meta http-equiv="Cache-Control" content="no-cache"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>
    <link rel="shortcut icon" href="<?= Root('favicon.ico') ?>" type="image/x-icon"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous">
    <style>
        html, body, #wrapper {
            height: 100%;
        }

        #wrapper {
        }

        #wrapper .page {
            padding: 30px;
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: calc(100% - 22px);
            justify-content: center;
        }

        #wrapper .page .table {
            max-width: 600px;
            margin: 0 auto;
        }

        #wrapper .page .table tr:last-child th,
        #wrapper .page .table tr:last-child td {
            border-bottom: none;
        }

        #wrapper .page .backtrace-wrapper {
            display: none;
        }

        #wrapper .page pre {
            background: #EEE;
            padding: 15px;
            border-radius: 6px;
        }

        #wrapper.debug-panel-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
<div id="wrapper">
    <?= $content ?>
    </div>
</body>
</html>
