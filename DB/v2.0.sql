
UPDATE `plugins` SET `type` = 'billing_service_provider' WHERE `plugins`.`name` = 'Reloadly';
UPDATE `plugins` SET `type` = 'billing_service_provider' WHERE `plugins`.`name` = 'Tpaga';
UPDATE `plugins` SET `type` = 'billing_service_provider' WHERE `plugins`.`name` = 'Flutterwave';
UPDATE `plugins` SET `type` = 'billing_service_provider' WHERE `plugins`.`name` = 'Bloc';
UPDATE `plugins` SET `type` = 'virtual_card_provider' WHERE `plugins`.`name` = 'Stripe Virtual Card';

INSERT INTO `plugins` (`id`, `icon`, `type`, `name`, `description`, `data`, `status`, `created_at`, `updated_at`) 
VALUES 
(NULL, 'global/plugin/ufitpay.png', 'virtual_card_provider', 'Ufitpay Virtual Card', 'A Ufitpay virtual card is a digital debit card used for secure online transactions.', '{"api_key": "","api_token": ""}', '1', '2023-12-19 12:45:26', '2024-03-17 11:10:56'),
(NULL, 'global/plugin/flutterwave.png', 'virtual_card_provider', 'Flutterwave Virtual Card', 'A Flutterwave virtual card is a digital debit card used for secure online transactions.', '{"secret_key":""}', '1', '2023-12-19 12:45:26', '2024-03-17 11:10:56');

INSERT INTO `themes` (`id`, `name`, `type`, `status`, `created_at`, `updated_at`) VALUES (NULL, 'digi_vault', 'site', '0', '2024-08-05 00:47:28', '2024-08-22 19:32:24');

ALTER TABLE `bill_services` CHANGE `label` `label` JSON NOT NULL;
ALTER TABLE `bill_services` CHANGE `charge_type` `charge_type` ENUM('fixed','percentage','flexible','range') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'fixed';
ALTER TABLE `bill_services` DROP `api_id`;
ALTER TABLE `bill_services` ADD `api_id` VARCHAR(50) NULL COMMENT 'service id from api' AFTER `id`;
ALTER TABLE `bill_services` CHANGE `code` `provider_code` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'flutterwave, bloc etc';
ALTER TABLE `bill_services` ADD UNIQUE (`api_id`);