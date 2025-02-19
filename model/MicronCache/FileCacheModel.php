<?php

namespace MicronCache;

use FileSys;

class FileCacheModel implements ICache
{
    private function getFileNameByKey(string $key): string
    {
        $file = md5($key);
        return BASEPATH . "tmp/file_cache/$file.json";
    }

    public function has(string $key): bool
    {
        $file = $this->getFileNameByKey($key);
        return is_readable($file);
    }

    public function get(string $key): mixed
    {
        $ret = null;
        if ($this->has($key)) {
            $file       = $this->getFileNameByKey($key);
            $cachedData = json_decode(FileSys::readFile($file), true);
            $ret        = $cachedData['data'];
        }
        return $ret;
    }

    public function set(string $key, mixed $data): int
    {
        $cache = [
            'key'              => $key,
            'data'             => $data,
            'create_time'      => time(),
            'last_update_time' => time(),
        ];
        $file  = $this->getFileNameByKey($key);
        return (int)FileSys::writeFile($file, json_encode($cache));
    }
}