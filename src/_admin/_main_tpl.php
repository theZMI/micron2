<?php

AutoTitle($content);
$title      = L('m_title');
$shortTitle = L('m_title');

if (!IS_ADMIN_AUTH) {
    Config('admin_menu', []);
    Config('admin_menu_right', []);
}

$logo = [
    'link'  => SiteRoot('_admin'),
    'logo'  => '<i class="bi bi-house-fill"></i>',
    'title' => 'Административный раздел',
    'css'   => ''
];