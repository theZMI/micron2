<?php

// Пользователи входят только по одноразовому коду из письма: пароли больше не хранятся (у админов пароли остаются).
// Таблица кодов переименована под новое назначение: user_pwd_recover -> user_auth_codes
return function ($db) {
    if (Migrator::tableExists($db, 'users') && Migrator::columnExists($db, 'users', 'pwd_hash')) {
        $db->query("ALTER TABLE `users` DROP COLUMN `pwd_hash`");
    }
    if (Migrator::tableExists($db, 'user_pwd_recover')) {
        Migrator::tableExists($db, 'user_auth_codes')
            ? $db->query("DROP TABLE `user_pwd_recover`") // Новая таблица уже создана моделью - старые коды не нужны
            : $db->query("RENAME TABLE `user_pwd_recover` TO `user_auth_codes`");
    }
};
