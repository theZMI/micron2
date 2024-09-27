<?php IncludeCom('_dev/syntaxhighlighter'); ?>

<div class="container">
    <div class="row">
        <div class="col">
            <h1 class="mt-4 mb-4">QR codes:</h1>
            <div class="row">
                <div class="col-12">
                    [code=Php]
                    // Создать qr-код и положить по указанному пути:
                    CreateQrCode('Hello world', BASEPATH . 'tmp/qrcodes/hello_world.png');

                    // Создать QR-код в 300х300 пикселях, с отступами в 30px и подписью '№ 0123456789 Test'
                    CreateQrCode('Hello world', BASEPATH . 'tmp/qrcodes/hello_world_with_label.png', 300, 30, '№ 0123456789 Test');
                    [/code]
                </div>
                <?php
                CreateQrCode('Hello world', BASEPATH . 'tmp/qrcodes/hello_world.png');
                CreateQrCode('Hello world', BASEPATH . 'tmp/qrcodes/hello_world_with_label.png', 300, 30, '№ 0123456789 Test');
                ?>
            </div>
        </div>
    </div>
</div>