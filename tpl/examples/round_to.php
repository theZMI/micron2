<?php IncludeCom('_dev/syntaxhighlighter'); ?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">Округление</h1>
            <div class="row">
                <div class="col-6">
                    Округление числа числа до сотен
                    [code=Php]
                    echo RoundToN(12345678, 100);
                    [/code]
                </div>
                <div class="col-6 pt-4">
                    <?php
                    echo RoundToN(12345678, 100);
                    ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-6">
                    Округление времени до 10 минут
                    [code=Php]
                    echo FormatDate( RoundToNMinutes(time(), 10) );
                    [/code]
                </div>
                <div class="col-6 pt-4">
                    <?php
                    echo date("d-m-Y H:i:s", RoundToNMinutes(time(), 10));
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

