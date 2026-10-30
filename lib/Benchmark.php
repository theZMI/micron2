<?php

require_once BASEPATH . 'trait/SingletonTrait.php';
require_once BASEPATH . 'lib/Benchmark/IBenchmark.php';
require_once BASEPATH . 'lib/Benchmark/Benchmark_DB.php';
require_once BASEPATH . 'lib/Benchmark/Benchmark_MockUp.php';

class Benchmark extends Benchmarks_DB implements IBenchmark
{
    use SingletonTrait;

    private function __construct() {}
}
