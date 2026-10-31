<?php

use ImageOrientationFix\ImageOrientationFixer;

ini_set('memory_limit', '1G');

$fileName       = '';
$isImageUpload  = $isImageUpload ?? false;
$defWidth       = $defWidth ?? 0;
$defHeight      = $defHeight ?? 0;
$uploader       = new Uploader();
$uploaderConfig = $uploader->getFinalConfig($uplConf);
$uplURL         = _StrReplaceFirst(BASEPATH, '', $uploaderConfig['upload_path']);
$hasThumbs      = count($uploaderConfig['thumbs']);
$thumbs         = ltrim(array_reduce($uploaderConfig['thumbs'], fn($total, $thumb) => $total .= "|{$thumb['width']}x{$thumb['height']}"), '|');

$isSet = Post("is_set_upl_form_{$uplName}");
if ($isSet) {
    $response = [
        'success'  => false,
        'fileName' => '',
        'errors'   => ''
    ];
    if ($uploader->HasUpload($uplName)) {
        $isUpload = $uploader->Upload(
            $uplName,
            $uplConf
        );
        if ($isUpload) {
            $uploadedFile = $uploader->GetInf('full_path');
            $isImage      = @is_array(getimagesize($uploadedFile));

            // Запоминаем, что файл загрузил этот посетитель: обрезать (_crop_image) он сможет только свои файлы
            $myUploads   = SessionManager::get('micron_my_uploads') ?: [];
            $myUploads[] = realpath($uploadedFile);
            SessionManager::set('micron_my_uploads', array_slice($myUploads, -20));
            if ($isImage) {
                try {
                    $iof = new ImageOrientationFixer($uploadedFile);
                    $iof->fix();
                } catch (\Throwable $e) {
                }
            }
            $response['success']  = true;
            $response['fileName'] = $uploader->GetInf('file_name');
            $response['errors']   = '';
        } else {
            $response['success']  = false;
            $response['fileName'] = '';
            $response['errors']   = implode(", ", $uploader->Errors());
        }
    }
    ob_clean();
    die(json_encode($response));
}