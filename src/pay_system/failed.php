<?php

$order = ShopOrderModel::findForCurrentVisitor();
if (!$order) {
    IncludeCom('404');
    ExitCom();
}
