<?php

$logo      = $logo ?? [];
$menu      = $menu ?? [];
$rightMenu = $rightMenu ?? [];
$incSearch = $incSearch ?? false;
$w100      = $w100 ?? false;

if ($incSearch) {
    $rightMenu = array_merge(
        [
            [
                'link'    => 'javascript:void(0)',
                'name'    => '<span class="d-inline d-lg-none">Поиск</span><i class="bi bi-search me-2 d-none d-lg-inline"></i>',
                'linkCss' => 'btn btn-link nav-link position-relative search-panel-button-toggle',
                'css'     => 'd-grid'
            ],
        ],
        $rightMenu
    );
}

if (empty($menu) && empty($rightMenu)) {
    ExitCom();
}


array_walk($menu, '__PrepareMenuElem');
foreach ($menu as & $v) {
    array_walk($v['list'], '__PrepareMenuElem');
}
unset($v);

foreach ($menu as $k => $v) {
    // Выделять если это текущая страница или страница в ее выпадающем списке
    $links = array($v['link']);
    $list  = empty($v['list']) ? [] : $v['list'];
    foreach ($list as $subLink) {
        if (is_array($subLink)) {
            if (!isset($subLink['link'])) {
                continue;
            }
            $links[] = $subLink['link'];
        }
    }

    if (in_array(GetCurUrl(), $links)) {
        $v['css'] = empty($v['css']) ? 'active' : "{$v['css']} active";
        $menu[$k] = $v;
    }
}

foreach ($menu as $k => $v) {
    if (count($v['list'])) {
        $v['css'] .= ' dropdown';
    }

    $v['css'] = trim($v['css']);
    $menu[$k] = $v;
}


array_walk($rightMenu, '__PrepareMenuElem');
foreach ($rightMenu as & $v) {
    array_walk($v['list'], '__PrepareMenuElem');
}
unset($v);


foreach ($rightMenu as $k => $v) {
    // Выделять если это текущая страница или страница в ее выпадающем списке
    $links = array($v['link']);
    $list  = empty($v['list']) ? [] : $v['list'];
    foreach ($list as $subLink) {
        if (!isset($subLink['link'])) {
            continue;
        }
        $links[] = $subLink['link'];
    }

    if (in_array(GetCurUrl(), $links)) {
        $v['css']      = empty($v['css']) ? 'active' : "{$v['css']} active";
        $rightMenu[$k] = $v;
    }
}

foreach ($rightMenu as $k => $v) {
    if (count($v['list'])) {
        $v['css'] .= ' dropdown';
    }

    $v['css']      = trim($v['css']);
    $rightMenu[$k] = $v;
}