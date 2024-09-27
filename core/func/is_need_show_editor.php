<?php

function IsNeedShowEditor($text, $name = null)
{
    return !($name == 'name' || stripos($name, '__noTags')) && HasHtml($text);
}