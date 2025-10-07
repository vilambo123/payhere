-- Add APK download URL to site_settings
-- Run this if you already have the site_settings table

USE `loan_system`;

-- Add APK download URL setting
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) 
VALUES ('apk_download_url', 'https://google.com', 'url', 'Mobile app APK download URL')
ON DUPLICATE KEY UPDATE setting_value = 'https://google.com';

-- Verify the setting was added
SELECT * FROM `site_settings` WHERE setting_key = 'apk_download_url';
