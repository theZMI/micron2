<?php

function MemoryHas(string $key): bool
{
    return MicronCache::getInstance()->has($key);
}

function MemoryGet(string $key): mixed
{
    return MicronCache::getInstance()->get($key);
}

function MemorySet(string $key, mixed $value): int
{
    return MicronCache::getInstance()->set($key, $value);
}