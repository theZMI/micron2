<?php

require_once BASEPATH . 'lib/CI_Upload.php';


class Uploader extends CI_Upload
{
    const FORM_LOAD = 'enctype="multipart/form-data" method="post"';

    protected $thumbPaths = [];

    public function getFinalConfig($config = [])
    {
        $finalConfig = Config(['uploader', 'default_config']);
        foreach ($config as $k => $v) {
            $finalConfig[$k] = $v;
        }
        return $finalConfig;
    }

    public static function createThumbs($source, $thumbs, $w, $h)
    {
        $aThumbs = pathinfo($thumbs);
        FileSys::makeDir($aThumbs['dirname']);
        $image = \WideImage\WideImage::load($source);
        $image->resize($w, $h, 'inside', 'down')
            ->crop('center', 'center', $w, $h)
            ->saveToFile($thumbs);
        return $thumbs;
    }

    public function upload($field, $config = [])
    {
        $finalConfig = $this->getFinalConfig($config);

        FileSys::makeDir($finalConfig['upload_path']);
        $this->initialize($finalConfig);

        $ret = $this->do_upload($field);

        if ($ret && is_array($finalConfig['thumbs'])) {
            $inf = $this->data();
            foreach ($finalConfig['thumbs'] as $v) {
                $newDir = $v['path'] ?? ($finalConfig['upload_path'] . $v['width'] . 'x' . $v['height'] . '/');
                $thumbs = $newDir . $inf['file_name'];

                self::createThumbs($inf['full_path'], $thumbs, $v['width'], $v['height']);

                /*
                FileSys::MakeDir($newDir);
                $image = WideImage::load($inf['full_path']);
                $image->resize($v['width'], $v['height'], 'outside')
                      ->crop('center', 'center', $v['width'], $v['height'])
                      ->saveToFile($newDir . $inf['file_name']);
                */

                $this->thumbPaths[] = $thumbs;//$newDir . $inf['file_name'];
            }
        }

        return $ret;
    }

    public function hasUpload($field)
    {
        return isset($_FILES[$field]) && isset($_FILES[$field]["tmp_name"]) && $_FILES[$field]["tmp_name"];
    }

    public function errors()
    {
        return $this->error_msg;
    }

    public function getInf($par = null)
    {
        $inf = $this->data();
        if (is_null($par)) {
            return $inf;
        }

        return isset($inf[$par]) ? $inf[$par] : null;
    }
}
