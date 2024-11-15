<?php

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

function CreateQrCode($text, $pathToSave = null, $imgSize = 240, $imgMargin = 30, $bottomLabel = null) {
    if ($pathToSave) {
        FileSys::makeDir(dirname($pathToSave));
    }

    $builder = Builder::create();
    $builder->writer(new PngWriter())
            ->writerOptions([])
            ->data($text)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->size($imgSize)
            ->margin($imgMargin)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->validateResult(false);
    if ($bottomLabel) {
        $builder->labelText($bottomLabel)
                ->labelFont(new OpenSans(16))
                // ->labelTextColor(new Color(0, 0, 0))
                ->labelAlignment(LabelAlignment::Center);
    }
    $result = $builder->build();

    return $pathToSave ? $result->saveToFile($pathToSave) : $result->getString();
}