<?php

// Переход на password_hash() и токены сессий: хешам паролей нужно больше места, кодам подтверждения - счётчик попыток
return function ($db) {
    foreach (['admins', 'users'] as $table) {
        if (Migrator::tableExists($db, $table)) {
            $db->query("ALTER TABLE ?# MODIFY `pwd_hash` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL", $table);
        }
    }

    if (Migrator::tableExists($db, 'user_pwd_recover')) {
        $db->query("DELETE FROM `user_pwd_recover`"); // Старые 5-значные коды больше не действуют
        $db->query("ALTER TABLE `user_pwd_recover` MODIFY `code` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL");
        if (!Migrator::columnExists($db, 'user_pwd_recover', 'attempts')) {
            $db->query("ALTER TABLE `user_pwd_recover` ADD `attempts` INT NOT NULL DEFAULT 0 AFTER `code`");
        }
    }
};
