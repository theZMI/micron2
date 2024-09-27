<?php

if (Get('a') === 'download') {
    echo ForceDownload(BASEPATH . 'i/image/logo.png', 'micron_logo.png');
}
