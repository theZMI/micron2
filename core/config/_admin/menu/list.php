<?php

$subMenu = [];
foreach (array_keys(Config('langs')) as $lang) {
    $subMenu[] = [
        'link'  => SiteRoot("_admin/list/list&lang={$lang}"),
        'name'  => "Страницы (<span class='text-uppercase'>$lang</span>)",
        'label' => "Редактирование содержимого статичных страниц"
    ];
}
$subMenu[] = [
    'link'  => SiteRoot('_admin/list/add'),
    'name'  => 'Новая страница',
    'label' => 'Создать новую страницу сайта'
];

Config(['admin_menu', 'PUSH'], [
    'link'  => 'javascript:void(0)',
    'name'  => 'Страницы',
    'label' => 'Добавление и изменение статичных страниц',
    'list'  => $subMenu
]);
unset($subMenu);