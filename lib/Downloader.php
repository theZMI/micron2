<?php

class Downloader
{
    private static function secure($file)
    {
        $file = strtr($file, ['..' => '']); // Что бы запретить попытки возврата вверх по файлам
        $file = realpath($file);
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (!file_exists($file)) {
            trigger_error("File {$file} not exists!", E_USER_ERROR);
        } elseif (!is_readable($file)) {
            trigger_error("File {$file} not readable!", E_USER_ERROR);
        } elseif (is_dir($file)) {
            trigger_error("Can not download directory!", E_USER_ERROR);
        }

        if (!in_array('*', Config('download_allow_filetypes'))) {
            if (!in_array($ext, Config('download_allow_filetypes'))) {
                trigger_error("Not allowed file type ($file -> $ext) for Downloader!", E_USER_ERROR);
            }
        }

        $isInAllowDir = false;
        foreach (Config('download_allow_dirs') as $dir) {
            if (stripos($file, $dir) !== false) {
                $isInAllowDir = true;
                break;
            }
        }
        if (!$isInAllowDir) {
            trigger_error("File {$file} from disallow directory for Downloader", E_USER_ERROR);
        }

        return $file;
    }

    public function download($file, $downloadName = '')
    {
        $file         = self::secure($file);
        $ext          = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        $downloadName = empty($downloadName) ? basename($file) : $downloadName;
        $mimes = Config('browserdatacache_mime_types') ?: [];
        $mime = $mimes[$ext] ?? 'application/octet-stream';
        $isIE         = isset($_SERVER['HTTP_USER_AGENT']) && strstr($_SERVER['HTTP_USER_AGENT'], "MSIE");

        header("Content-Type: {$mime}");
        header("Content-Disposition: attachment; filename={$downloadName}");
        header("Content-Transfer-Encoding: binary");
        header('Expires: 0');
        if ($isIE) {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Pragma: no-cache');
        }
        header("Content-Length: " . filesize($file));
        readfile($file);
        exit();
    }
}
