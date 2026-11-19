<?php

$list                  = (new ShopOrderModel())->getAll();
$sign                  = Config('shop')['currency_sign'];
$tableHeader           = ['№', 'Дата', 'Покупатель', 'E-mail', 'Телефон', 'Сумма', 'Статус'];
$tableHeaderEnd        = '<th width="1%">Действия</th>';
$tableHeaderEndFilters = '<td></td>';
$colWidths             = ['№' => '60', 'Покупатель' => 'auto'];
$tableData             = [];
$tableDataEnd          = [];
foreach ($list as $v) {
    $tableData[$v->id] = [
        $v->id,
        date('d.m.Y H:i', $v->create_time),
        $v->customer_name,
        $v->customer_email,
        $v->customer_phone ? OutputFormats::mobilePhone($v->customer_phone) : '',
        ShopPrice($v->total_amount),
        $v->statusName(),
    ];
    $tableDataEnd[$v->id] = '<td width="1%" class="text-center"><a href="' . SiteRoot("_admin/shop/order&id={$v->id}") . '" class="btn btn-sm btn-primary rounded-pill default-click" title="Открыть"><i class="bi bi-eye-fill"></i></a></td>';
}
$defaultTableValues = [];
$tableFilters       = [
    'Покупатель' => 'input',
    'E-mail'     => 'input',
    'Статус'     => 'select',
];
