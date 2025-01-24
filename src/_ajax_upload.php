<?php

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
            if ($isImage) {
                fix_orientation($uploadedFile);
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