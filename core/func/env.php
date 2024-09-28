<?php

function Env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}