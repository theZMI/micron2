<?php

class UrlRedirect
{
    public static function go($url = null, $status = 302, $pGoFunc = null)
    {
        if (!empty($pGoFunc)) {
            call_user_func($pGoFunc, $url, $status);
        }

        if ($url) {
            header("Location: " . $url, true, $status);
        }
        exit;
    }
}
