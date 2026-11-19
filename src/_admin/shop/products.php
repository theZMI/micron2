<?php

$list = (new ShopProductModel())->getAll();

if (Post('a') === 'delete') {
    if (isset($list[intval(Post('id'))])) {
        $list[intval(Post('id'))]->delete(); // В заказах остаются копии названия и цены, поэтому удалять товар безопасно
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}

$sign                  = Config('shop')['currency_sign'];
$tableHeader           = ['ID', 'Название', 'Артикул', 'Категория', 'Цена', 'Остаток', 'Рейтинг', 'В продаже', 'Порядок'];
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
        $v->category_id ? ShopCategoryModel::pathName($v->category_id) : '—',
        ShopPrice($v->price) . $v->priceUnitSuffix(),
        $v->track_stock ? ShopQuantity($v->stock, $v->unitKey()) : '—',
        $v->reviews_count ? number_format($v->rating, 1, ',', '') . " ({$v->reviews_count})" : '—',
        $v->is_active ? 'Да' : 'Нет',
        $v->sort,
    ];
    ob_start();
    ?>
    <td width="1%" class="text-center" style="display: none;">
        <a href="<?= SiteRoot("_admin/shop/products/add_or_edit&id={$v->id}") ?>" class="btn btn-sm btn-primary rounded-pill default-click" title="Изменить"><i class="bi bi-pencil-fill"></i></a>
    </td>
    <td width="1%" class="text-center">
        <?= PostActionButton('delete', ['id' => $v->id], 'Удалить товар?', 'btn btn-sm btn-danger rounded-pill') ?>
    </td>
    <?php
    $tableDataEnd[$v->id] = ob_get_clean();
}
$defaultTableValues = [];
$tableFilters       = [
    'Название'  => 'input',
    'Артикул'   => 'input',
    'Категория' => 'select',
    'В продаже' => 'select',
];
