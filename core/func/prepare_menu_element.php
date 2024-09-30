<?php

function __PrepareMenuElem(&$v, $k = null)
{
    if (is_array($v)) {
        if (!isset($v['list'])) {
            $v['list'] = [];
        }
        if (!isset($v['css'])) {
            $v['css'] = '';
        }
        if (!isset($v['label'])) {
            $v['label'] = '';
        }
        if (!isset($v['html'])) {
            $v['html'] = '';
        }
        if (!isset($v['linkCss'])) {
            $v['linkCss'] = '';
        }
    }
}