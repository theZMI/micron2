<?php

// Этот файл должен вызываться, когда меню уже заполнено. Добавляем в конце кнопку 'выход':
Config(['admin_menu_right', 'PUSH'], [
    'link'  => SiteRoot('_admin/logout'),
    'name'  => '<span class="bi bi-power"></span>',
    'label' => 'Выход',
    'css'   => '',
    'list'  => [],
]);
