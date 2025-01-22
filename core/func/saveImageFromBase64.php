<?php

function SaveImageFromBase64($base64, $pathWithoutExt) {
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
        $data = substr($base64, strpos($base64, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
            throw new \Exception('invalid image type');
        }
        $data = str_replace( ' ', '+', $data);
        $data = base64_decode($data);

        if ($data === false) {
            throw new \Exception('base64_decode failed');
        }
    } else {
        throw new \Exception('did not match data URI with image data');
    }

    return "{$pathWithoutExt}.{$type}";
};