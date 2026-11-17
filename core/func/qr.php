<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

function CreateQrCode($text, $pathToSave = null, $imgSize = 240, $imgMargin = 30, $bottomLabel = null) {
    if ($pathToSave) {
        FileSys::makeDir(dirname($pathToSave));
    }

    // endroid/qr-code 6: все настройки передаются в конструктор (раньше - Builder::create() и цепочка сеттеров)
    $builder = new Builder(
        writer: new PngWriter(),
        writerOptions: [],
        validateResult: false,
        data: (string)$text,
        encoding: new Encoding('UTF-8'),
        errorCorrectionLevel: ErrorCorrectionLevel::High,
        size: $imgSize,
        margin: $imgMargin,
        roundBlockSizeMode: RoundBlockSizeMode::Margin,
        labelText: (string)$bottomLabel,
        labelFont: new OpenSans(16),
        labelAlignment: LabelAlignment::Center,
    );
    $result = $builder->build();

    return $pathToSave ? $result->saveToFile($pathToSave) : $result->getString();
}