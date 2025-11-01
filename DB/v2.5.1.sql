UPDATE `gateways` SET `is_withdraw` = 'address', `credentials` = '{\"api_key\":\"\",\"secret_key\":\"\",\"email\":\"\",\"password\":\"\"}'  WHERE `gateways`.`gateway_code` = 'nowpayments';
ALTER TABLE `loan_plans` ADD `loan_fee_type` ENUM('percentage','fixed') NOT NULL DEFAULT 'fixed' AFTER `loan_fee`;
