CREATE TABLE `shortener` (
	`short_url` char(6) COLLATE utf8_unicode_ci NOT NULL,
	`real_url` text(255) COLLATE utf8_unicode_ci NOT NULL,
	`visits` bigint(10) NOT NULL DEFAULT '0',
	`date_added` datetime NOT NULL,
	PRIMARY KEY (`short_url`)
)