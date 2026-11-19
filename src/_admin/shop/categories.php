<?php

// Категории товаров: дерево
if (Post('a') === 'delete') {
    $category = new ShopCategoryModel(intval(Post('id')) ?: null);
    if ($category->isExists()) {
        $category->delete(); // Подкатегории перейдут к родителю, товары останутся без категории
    }
    UrlRedirect::go(GetCurUrl('a=' . M_DELETE_PARAM . '&id=' . M_DELETE_PARAM));
}

$counts  = ShopCategoryModel::productCounts();
$options = ShopCategoryModel::selectOptions(); // Порядок дерева и отступы
$rows    = ShopCategoryModel::rows();
