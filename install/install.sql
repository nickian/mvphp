CREATE TABLE IF NOT EXISTS `users` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
	`password` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
	`status` tinyint(2) unsigned NOT NULL DEFAULT '0',
	`verified` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`resettable` tinyint(1) unsigned NOT NULL DEFAULT '1',
	`roles_mask` int(10) unsigned NOT NULL DEFAULT '0',
	`registered` int(10) unsigned NOT NULL,
	`last_login` int(10) unsigned DEFAULT NULL,
	`force_logout` mediumint(7) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_confirmations` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`user_id` int(10) unsigned NOT NULL,
	`email` varchar(249) COLLATE utf8mb4_unicode_ci NOT NULL,
	`selector` varchar(16) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`expires` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `selector` (`selector`),
	KEY `email_expires` (`email`,`expires`),
	KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_remembered` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`user` int(10) unsigned NOT NULL,
	`selector` varchar(24) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`expires` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `selector` (`selector`),
	KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_resets` (
	`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
	`user` int(10) unsigned NOT NULL,
	`selector` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`token` varchar(255) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`expires` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `selector` (`selector`),
	KEY `user_expires` (`user`,`expires`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users_throttling` (
	`bucket` varchar(44) CHARACTER SET latin1 COLLATE latin1_general_cs NOT NULL,
	`tokens` float unsigned NOT NULL,
	`replenished_at` int(10) unsigned NOT NULL,
	`expires_at` int(10) unsigned NOT NULL,
	PRIMARY KEY (`bucket`),
	KEY `expires_at` (`expires_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   
CREATE TABLE IF NOT EXISTS `settings` (
	`id` int(10) unsigned NOT NULL,
	`name` varchar(40) DEFAULT NULL,
	`value` varchar(100) DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
   
CREATE TABLE IF NOT EXISTS `hosts` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`host` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `host` (`host`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
