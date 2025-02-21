<?php

/**
 * @param string $path - путь от XL-файла
 * @param array $nDateFields - номера колонок в которых хранятся даты (в XL формате)
 * @return array
 */
function ReadXL(string $path, $nDateFields = [])
{
    $getXLReader = function($path) {
        return match (pathinfo($path, PATHINFO_EXTENSION)) {
            'cvs'   => new \PhpOffice\PhpSpreadsheet\Reader\Csv($path),
            'xls'   => new \PhpOffice\PhpSpreadsheet\Reader\Xls($path),
            default => new \PhpOffice\PhpSpreadsheet\Reader\Xlsx($path),
        };
    };
    $convertXLDatesToTimestamps = function() use (&$res, &$nDateFields) {
        if (count($nDateFields)) {
            foreach ($res as $k => $row) {
                foreach ($row as $n => $cell) {
                    if (in_array($n, $nDateFields)) {
                        if (!is_numeric($cell)) {
                            continue;
                        }
                        $res[$k][$n] = FromExcelToLinux($cell);
                    }
                }
            }
        }
    };

    $reader = $getXLReader($path);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($path);
    $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
    $res = $sheet->toArray();
    $convertXLDatesToTimestamps();
    return $res;
}

function FromExcelToLinux($excelTime) // $excelTime - это количество дней от 1900-го года
{
    return ($excelTime - 25569) * 86400; // 70лет = 25569дней, 86400секунд в дне
}

function DownloadAsXL($rows, $fileName)
{
    $toCellChar = function($cellN) {
        $alphabet = range('A', 'Z');
        if ($cellN >= 26) {
            $firstDigit = intval($cellN / 26);
            $alpha      = $alphabet[$firstDigit] . $alphabet[$cellN % 26];
        } else {
            $alpha = $alphabet[$cellN];
        }

        return $alpha;
    };

    $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $objPHPExcel->getProperties()->setCreator("PHP");

    $rowN  = 0;
    foreach ($rows as $row) {
        $rowN++;
        $cellN = 0;
        foreach ($row as $data) {
            $alpha = $toCellChar($cellN);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue(
                "{$alpha}{$rowN}",
                strip_tags(
                    htmlspecialchars_decode($data)
                )
            );
            $cellN++;
        }
    }

    // Save xl-file
    $path   = BASEPATH . "upl/$fileName";
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($objPHPExcel);
    $writer->save($path);

    // Send this file for downloading
    ob_clean();
    (new Downloader())->Download($path, $fileName);
    die;
}