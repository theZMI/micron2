<?php

function RoundToN($value, $roundTo = 100)
{
    return round($value / $roundTo) * $roundTo;
}

function RoundToNMinutes($timestamp, $minutes = 5)
{
    return round($timestamp / ($minutes * 60)) * ($minutes * 60);
}