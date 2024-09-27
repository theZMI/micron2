<?php

function ForceDownload($file, $downloadName = '')
{
    return (new Downloader())->download($file, $downloadName);
}