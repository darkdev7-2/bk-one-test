
INSERT INTO `plugins` (`id`, `icon`, `type`, `name`, `description`, `data`, `status`, `created_at`, `updated_at`) VALUES
(DEFAULT, 'global/plugin/stripe.png', 'system', 'Stripe Virtual Card', 'A Stripe virtual card is a digital credit card used for secure online transactions.', '{\"secret_key\":\"\"}', 0, '2024-08-21 06:45:26', '2024-08-21 06:45:26');

INSERT INTO `themes` (`id`, `name`, `type`, `status`, `created_at`, `updated_at`) VALUES
(DEFAULT, 'corporate', 'site', 0, '2024-08-04 18:47:28', '2024-08-22 13:32:24');

INSERT INTO `permissions` (`id`, `category`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(151, 'Customer Management', 'user-paybacks-tab', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(152, 'Customer Management', 'user-cards', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(153, 'Customer Management', 'user-dps', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(154, 'Customer Management', 'user-fdr', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(155, 'Customer Management', 'user-loan', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),

(156, 'Wallet Management', 'wallet-list', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),

(157, 'Virtual Card Management', 'virtual-card-list', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(158, 'Virtual Card Management', 'virtual-card-topup', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46'),
(159, 'Virtual Card Management', 'virtual-card-status-change', 'admin', '2024-09-17 09:22:46', '2024-09-17 09:22:46');

-- --------------------------------------------------------
--
-- Create table for `currencies`
--
CREATE TABLE `currencies` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Create table for `user_wallets`
--
CREATE TABLE `user_wallets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `currency_id` bigint unsigned NOT NULL,
  `balance` decimal(20,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_wallets_user_id_foreign` (`user_id`),
  KEY `user_wallets_currency_id_foreign` (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE beneficiaries
ADD COLUMN user_id bigint AFTER bank_id;

-- --------------------------------------------------------
--
-- ALTER TABLE `transactions`
--
ALTER TABLE `transactions`
    ADD COLUMN `wallet_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'default',
    ADD COLUMN `card_id` int UNSIGNED DEFAULT NULL;

-- --------------------------------------------------------
--
-- Inserting data for table `user_navigations`
--
INSERT INTO `user_navigations` (`id`, `icon`, `url`, `type`, `name`, `position`, `translation`, `created_at`, `updated_at`) VALUES
(16, 'credit-card', 'user/cards', 'cards', 'Virtual Cards', 16, NULL, NULL, '2024-05-13 10:47:22'),
(17, 'wallet', 'user/wallets', 'wallets', 'Wallets', 17, NULL, NULL, '2024-05-13 10:47:22');

-- --------------------------------------------------------
--
-- Create table for `card_holders`
--
CREATE TABLE `card_holders` (
    `id` bigint unsigned NOT NULL AUTO_INCREMENT,
    `user_id` bigint unsigned NOT NULL,
    `card_holder_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `provider` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phone_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
    `type` enum('individual','business') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
    `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `postal_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT NULL,
    `updated_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
--
-- Create table for `cards`
--
CREATE TABLE `cards` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL, 
  `card_holder_id` bigint unsigned NOT NULL, 
  `card_id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'virtual',
  `status` enum('active','inactive') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inactive',
  `amount` decimal(8,2) NOT NULL DEFAULT '0.00',
  `expiration_month` int NOT NULL,
  `expiration_year` int NOT NULL,
  `last_four_digits` varchar(4) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


