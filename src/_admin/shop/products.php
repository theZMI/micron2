<?php

$list = (new ShopProductModel())->getAll();

if (Get('a') === 'delete') {
    if (isset($list[intval(Get('id'))])) {
        $list[intval(Get('id'))]->delete(); // В заказах остаются копии названия и цены, поэтому удалять товар безопасно
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}

$sign                  = Config('shop')['currency_sign'];
$tableHeader           = ['ID', 'Название', 'Артикул', 'Цена', 'В продаже', 'Порядок'];
$tableHeaderEnd        = '<th width="1%" colspan="2">Действия</th>';
$tableHeaderEndFilters = '<td colspan="2"></td>';
$colWidths             = ['ID' => '50', 'Название' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $v) {
    $tableData[$v->id] = [
        $v->id,
        $v->name,
        $v->vendor_code,
        OutputFormats::amount($v->price, 2, $sign),
        $v->is_active ? 'Да' : 'Нет',
        $v->sort,
    ];
    ob_start();
    ?>
    <td width="1%" class="text-center" style="display: none;">
        <a href="<?= SiteRoot("_admin/shop/products/add_or_edit&id={$v->id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
    </td>
    <td width="1%" class="text-center">
        <a href="<?= GetCurUrl('a=delete&id=' . $v->id) ?>" class="btn btn-sm btn-danger rounded-pill" onclick="return confirm('Удалить товар?')" title="Удалить"><i class="bi bi-trash3-fill"></i></a>
    </td>
    <?php
    $tableDataEnd[$v->id] = ob_get_clean();
}
$defaultTableValues = [];
$tableFilters       = [
    'Название'  => 'input',
    'Артикул'   => 'input',
    'В продаже' => 'select',
];
