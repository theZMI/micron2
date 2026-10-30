<?php

require_once BASEPATH . 'lib/Benchmark.php';

function MemoryInFormat($size)
{
    $size  = sprintf("%u", $size);
    $names = [" Bytes", " Kb", " Mb", " Gb", " Tb", " Pb", " Eb", " Zb", " Yb"];
    return $size ? round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $names[$i] : '0 Bytes';
}

function BM($label): void
{
    Benchmark::getInstance()->add($label);
}
