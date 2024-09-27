<?php

/**
 * Функция установки title если страница не главная и не было установлено значений до этого
 */
function AutoTitle($content)
{
    global $g_lang;

    if (L('m_title') === L('m_defaultTitle')) {
        preg_match('~<h1(.*?)>(.*?)</h1>~is', $content, $m);
        if (isset($m[2])) {
            $g_lang['m_title'] = strip_tags($m[2]) . L('m_titlePostfix');
        }
    }
}