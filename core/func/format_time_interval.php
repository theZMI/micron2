<?php

function FormatTimeInterval(int $seconds, $withSeconds = false): string
{
    return $withSeconds
        ? sprintf('%02d:%02d:%02d', intval($seconds / 3600), intval($seconds / 60) % 60, $seconds % 60)
        : sprintf('%02d:%02d', intval($seconds / 3600), intval($seconds / 60) % 60);
}