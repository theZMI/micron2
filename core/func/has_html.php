<?php

function HasHtml($content)
{
    if (empty($content)) {
        return false;
    }
    return strlen($content) != strlen(strip_tags($content));
}