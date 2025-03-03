DROP TABLE IF EXISTS admins;

CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `surname` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `patronymic` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pwd_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `desc` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `create_time` int DEFAULT NULL,
  `last_update_time` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

INSERT INTO admins VALUES("1","admin","","","","","","043009bebf8b9db96da7159a878c32aa","","1741014287","");



DROP TABLE IF EXISTS error_logger;

CREATE TABLE `error_logger` (
  `id` int NOT NULL AUTO_INCREMENT,
  `_get` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `_post` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `_cookie` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `_session` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `_server` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `_files` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `backtrace` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `sql` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ip` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browser` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `browser_version` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `platform` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `g_config` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `g_lang` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `g_user` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `errno` int DEFAULT NULL,
  `errstr` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `errfile` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `errline` int DEFAULT NULL,
  `create_time` int DEFAULT NULL,
  `last_update_time` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;




DROP TABLE IF EXISTS users;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `login` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `avatar` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telegram_login` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telegram_chat_id` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pwd_hash` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `surname` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `patronymic` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `job_title` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` int DEFAULT NULL,
  `gender` int DEFAULT NULL,
  `birthday_date` date DEFAULT NULL,
  `is_phone_verified` int DEFAULT NULL,
  `notification_by_phone` int DEFAULT NULL,
  `is_email_verified` int DEFAULT NULL,
  `notification_by_email` int DEFAULT NULL,
  `is_telegram_verified` int DEFAULT NULL,
  `notification_by_telegram` int DEFAULT NULL,
  `last_location` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` int DEFAULT NULL,
  `create_time` int DEFAULT NULL,
  `last_update_time` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;




