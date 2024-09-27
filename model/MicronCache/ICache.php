<?php

namespace MicronCache;

interface ICache
{
    public function has(string $key): bool;

    public function get(string $key): mixed;

    public function set(string $key, mixed $data): int;
}