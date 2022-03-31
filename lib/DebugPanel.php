<?php

/**
 * Информационная панель
 */
class DebugPanel
{
    const PHP_INI_USER   = 1;
    const PHP_INI_PERDIR = 2;
    const PHP_INI_SYSTEM = 4;
    const PHP_INI_ALL    = 7;

    public function files()
    {
        $files = get_included_files();
        $stat  = [];
        foreach ($files as $file) {
            $stat[] = [
                'file'  => $file,
                'size'  => FileSys::size($file),
                'lines' => count(file($file))
            ];
        }
        return $stat;
    }

    private function formatBytes($size)
    {
        $size  = sprintf("%u", $size);
        $names = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'];
        return $size ? (round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . " {$names[$i]}") : '0 Bytes';
    }

    public function totalFileSize()
    {
        $total = 0;
        array_filter(
            get_included_files(),
            function ($f) use (&$total) {
                $total += filesize($f);
            }
        );
        return $this->formatBytes($total);
    }

    public function totalFileLines()
    {
        $total = 0;
        array_filter(
            get_included_files(),
            function ($f) use (&$total) {
                $total += count(file($f));
            }
        );
        return $total;
    }

    public function db()
    {
        return class_exists('MyDataBaseLog') ? MyDataBaseLog::render() : '';
    }

    public function memoryUsage()
    {
        return $this->formatBytes(memory_get_usage());
    }

    public static function showPhpIniAccess($access)
    {
        switch ($access) {
            case self::PHP_INI_USER:
                $ret = 'scripts';
                break;
            case 6:
            case self::PHP_INI_PERDIR:
                $ret = 'php.ini | .htaccess | httpd.conf';
                break;
            case self::PHP_INI_SYSTEM:
                $ret = 'php.ini | httpd.conf';
                break;
            case self::PHP_INI_ALL:
                $ret = 'anywhere';
                break;
            default:
                $ret = '-';
        };

        return $ret;
    }
}
