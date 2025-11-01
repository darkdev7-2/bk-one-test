ALTER TABLE `bill_services` ADD `code` VARCHAR(255) NULL DEFAULT NULL AFTER `api_id`;
ALTER TABLE `bills` ADD `response_data` TEXT NULL DEFAULT NULL AFTER `data`;