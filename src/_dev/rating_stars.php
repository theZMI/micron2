<?php

$rating = $rating ?? -1;

if (!isset($jsCallback)) {
    throw new RuntimeException('You must set jsCallback into _dev/rating_stars component');
}