<?php

/**
 * Примеры использования:
 *
 * IncludeCom("_dev/paginator",
 *     array
 *     (
 *         "pageUrl"      => SiteRoot("_dev/pagesbar_example&page=" . M_PAGINATOR_PAGE),
 *         "firstPageUrl" => SiteRoot("_dev/pagesbar_example"),
 *         "total"        => 400,
 *         "perPage"      => 11,
 *         "curPage"      => Get("page", 1)
 *     )
 * );
 *
 * IncludeCom("_dev/paginator",
 *     array
 *     (
 *         "pageUrl"      => GetCurUrl("page=" . M_PAGINATOR_PAGE),
 *         "firstPageUrl" => GetCurUrl("page=" . M_DELETE_PARAM),
 *         "total"        => 400,
 *         "perPage"      => 11,
 *         "curPage"      => Get("page", 1)
 *     )
 * );
 */

if (!defined('M_PAGINATOR_PAGE')) {
    define('M_PAGINATOR_PAGE', 'paginator_page_number');
}

if (empty($pageUrl)) {
    trigger_error("Empty page 'pageUrl'", E_USER_ERROR);
}
if (strpos($pageUrl, M_PAGINATOR_PAGE) === false) {
    trigger_error("Can't find M_PAGINATOR_PAGE constant", E_USER_ERROR);
}

// Параметр firstPageUrl может быть пустым. Тогда мы берем $pageUrlс номером страницы 1:
$firstPageUrl = !empty($firstPageUrl) ? $firstPageUrl : str_replace(M_PAGINATOR_PAGE, 1, $pageUrl);

$total   = max(0, intval($total));
$curPage = max(1, intval($curPage));
$perPage = intval($perPage);

if ($perPage < 1) {
    trigger_error("Invalid per page count " . $perPage, E_USER_ERROR);
}

if ($perPage >= $total) { // Бар выводится если страниц больше чем одной
    ExitCom();
} else {
    $totalPages = ceil($total / $perPage);
    $curPage    = min($totalPages, $curPage);

    $arrowLeft   = null;
    $pagesLeft   = [];
    $pagesCenter = [];
    $pagesRight  = [];
    $arrowRight  = null;

    $howManyLinks = 7;

    $temp = ceil($totalPages / $howManyLinks);
    for ($i = 1; $i <= $temp; $i++) {
        if ($curPage > ($i - 1) * $howManyLinks && $curPage <= $i * $howManyLinks) {
            $left  = ($i - 1) * $howManyLinks + 1;
            $right = $i * $howManyLinks;
            break;
        }
    }
    $borders = ['left' => intval($left), 'right' => intval($right)];

    $leftBar  = $curPage - 3 < 1 ? 1 : $curPage - 3;
    $rightBar = $leftBar + 6 > $totalPages ? $totalPages : $leftBar + 6;

    $totalPagesVisible = ($rightBar - $curPage) + ($curPage - $leftBar);

    if ($totalPagesVisible < 6) {
        $leftBar = $leftBar - (6 - $totalPagesVisible) < 1 ? 1 : $leftBar - (6 - $totalPagesVisible);
    }

    if ($curPage > 1) {
        $arrowLeft = ($curPage - 1) == 1 ? $firstPageUrl : str_replace(M_PAGINATOR_PAGE, $curPage - 1, $pageUrl);

        if ($leftBar == 2) {
            $pagesCenter[] = 1;
        }
        if ($leftBar == 3) {
            $pagesCenter[] = 1;
            $pagesCenter[] = 2;
        }
        if ($leftBar > 3) {
            $pagesLeft[] = 1;
        }
    }

    for ($i = $leftBar; $i <= $rightBar; $i++) {
        $pagesCenter[] = $i;
    }

    if ($rightBar < $totalPages) {
        if ($rightBar == $totalPages - 2) {
            $pagesCenter[] = $rightBar + 1;
        }
        if ($rightBar < $totalPages - 2) {
            $pagesRight[] = $totalPages;
        } else {
            $pagesCenter[] = $totalPages;
        }
    }
    if ($curPage < $totalPages) {
        $arrowRight = str_replace(M_PAGINATOR_PAGE, $curPage + 1, $pageUrl);
    }

    foreach ($pagesLeft as $k => $v) {
        $pagesLeft[$k] = [
            "href"      => ($v == 1 ? $firstPageUrl : str_replace(M_PAGINATOR_PAGE, $v, $pageUrl)),
            "page"      => $v,
            "is_active" => ($v == $curPage),
        ];
    }
    foreach ($pagesCenter as $k => $v) {
        $pagesCenter[$k] = [
            "href"      => ($v == 1 ? $firstPageUrl : str_replace(M_PAGINATOR_PAGE, $v, $pageUrl)),
            "page"      => $v,
            "is_active" => ($v == $curPage),
        ];
    }
    foreach ($pagesRight as $k => $v) {
        $pagesRight[$k] = [
            "href"      => ($v == 1 ? $firstPageUrl : str_replace(M_PAGINATOR_PAGE, $v, $pageUrl)),
            "page"      => $v,
            "is_active" => ($v == $curPage),
        ];
    }

    if (!empty($template) && $template != "_dev/paginator") {
        IncludeCom($template, [
            "arrowLeft"   => $arrowLeft,
            "pagesLeft"   => $pagesLeft,
            "pagesCenter" => $pagesCenter,
            "pagesRight"  => $pagesRight,
            "arrowRight"  => $arrowRight,
        ]);
        ExitCom();
    }
}
