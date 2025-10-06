-- =====================================================
-- Loan System Database Setup
-- For XAMPP / phpMyAdmin
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS `loan_system` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;

USE `loan_system`;

-- =====================================================
-- Table: inquiries
-- Store loan application inquiries from customers
-- =====================================================
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `loan_type` varchar(50) NOT NULL,
  `loan_amount` decimal(15,2) NOT NULL,
  `monthly_income` decimal(15,2) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','contacted','approved','rejected') DEFAULT 'pending',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Table: loan_types
-- Reference table for loan types and their settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `loan_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_name` varchar(50) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `min_amount` decimal(15,2) NOT NULL,
  `max_amount` decimal(15,2) NOT NULL,
  `min_interest_rate` decimal(5,2) NOT NULL,
  `max_tenure_years` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `type_name` (`type_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Insert default loan types
-- =====================================================
INSERT INTO `loan_types` (`type_name`, `display_name`, `min_amount`, `max_amount`, `min_interest_rate`, `max_tenure_years`, `description`, `is_active`) VALUES
('personal', 'Personal Loan', 5000.00, 200000.00, 3.50, 10, 'Quick cash for any personal need with flexible repayment terms', 1),
('business', 'Business Loan', 10000.00, 1000000.00, 4.00, 15, 'Grow your business with our competitive business financing solutions', 1),
('home', 'Home Loan', 50000.00, 2000000.00, 3.20, 35, 'Make your dream home a reality with affordable home financing', 1),
('car', 'Car Loan', 20000.00, 500000.00, 2.80, 9, 'Drive your dream car today with hassle-free auto financing', 1);

-- =====================================================
-- Table: contacts
-- Store general contact form submissions
-- =====================================================
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_read` (`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Table: site_settings
-- Store website configuration and settings
-- =====================================================
CREATE TABLE IF NOT EXISTS `site_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(50) DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Insert default site settings
-- =====================================================
INSERT INTO `site_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'QuickLoan', 'text', 'Website name'),
('site_email', 'info@quickloan.com', 'email', 'Contact email address'),
('site_phone', '+60 3-1234 5678', 'text', 'Contact phone number'),
('site_address', 'Kuala Lumpur, Malaysia', 'text', 'Business address'),
('social_facebook', 'https://facebook.com/quickloan', 'url', 'Facebook page URL'),
('social_twitter', 'https://twitter.com/quickloan', 'url', 'Twitter profile URL'),
('social_instagram', 'https://instagram.com/quickloan', 'url', 'Instagram profile URL'),
('social_linkedin', 'https://linkedin.com/company/quickloan', 'url', 'LinkedIn company URL'),
('enable_notifications', '1', 'boolean', 'Enable email notifications for new inquiries'),
('maintenance_mode', '0', 'boolean', 'Enable maintenance mode');

-- =====================================================
-- Sample data for testing (Optional)
-- =====================================================
INSERT INTO `inquiries` (`name`, `email`, `phone`, `loan_type`, `loan_amount`, `monthly_income`, `message`, `status`) VALUES
('Ahmad Rahman', 'ahmad@example.com', '+60123456789', 'business', 150000.00, 8000.00, 'Need funding for expanding my retail business', 'pending'),
('Sarah Lee', 'sarah@example.com', '+60198765432', 'home', 500000.00, 12000.00, 'Looking for home loan for first property', 'contacted'),
('David Tan', 'david@example.com', '+60187654321', 'personal', 50000.00, 5000.00, 'Personal loan for wedding expenses', 'approved');

-- =====================================================
-- Views for reporting (Optional but useful)
-- =====================================================
CREATE OR REPLACE VIEW `inquiry_summary` AS
SELECT 
    loan_type,
    COUNT(*) as total_inquiries,
    SUM(loan_amount) as total_amount_requested,
    AVG(loan_amount) as average_amount,
    status
FROM inquiries
GROUP BY loan_type, status
ORDER BY loan_type, status;

-- =====================================================
-- End of database setup
-- =====================================================
