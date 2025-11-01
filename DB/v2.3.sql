
ALTER TABLE users ADD custom_fields_data JSON NOT NULL AFTER otp;

Insert INTO `permissions` ( `category`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
('App Setting Management', 'app-settings', 'admin', NULL, NULL);
