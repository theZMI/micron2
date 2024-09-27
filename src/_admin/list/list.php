<?php

if (!function_exists("CreateLinkFromElement")) {
    function CreateLinkFromElement($uri)
    {
        global $g_lang;
        $lang    = Get('lang', DEF_LANG);
        $uri     = str_replace(Config(['admin_list', 'dir']), '', $uri);
        $urlEdit = SiteRoot("_admin/list/edit&lang={$lang}&file={$uri}");
        $urlDel  = SiteRoot("_admin/list/del&lang={$lang}&file={$uri}");

        $names = $g_lang['admin_list_names'] ?? [];
        ob_start();
        ?>
        <table class="table table-condensed table-hover align-middle">
            <tr>
                <td><?= isset($names[$uri]) ? "{$uri} <span class='badge bg-secondary'>{$names[$uri]}</span>" : $uri; ?></td>
                <td width="1%" class="text-center">
                    <a href="<?= $urlEdit ?>" class="btn btn-sm btn-primary" title="Редактировать страницу"><span class="bi bi-pencil-fill"></span></a>
                </td>
                <td width="1%" class="text-center">
                    <a href="<?= $urlDel ?>" class="btn btn-sm btn-danger" title="Удалить страницу" onclick='return confirm("Хотите удалить страницу?")'><span class="bi bi-trash3-fill"></span></a>
                </td>
            </tr>
        </table>
        <?php
        return ob_get_clean();
    }
}

$lang = Get('lang');
Config(['admin_list', 'dir'], BASEPATH . "lang/{$lang}/");

$dirs       = FileSys::ReadList(Config(['admin_list', 'dir']));
$exceptions = [];
foreach (Config(['admin_list', 'exceptions']) as $v) {
    $exceptions[] = Config(['admin_list', 'dir']) . $v;
}