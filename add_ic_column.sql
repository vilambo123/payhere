-- Add IC number column to existing inquiries table
-- Run this if you already have the inquiries table

USE `loan_system`;

-- Add ic_number column after phone
ALTER TABLE `inquiries` 
ADD COLUMN `ic_number` varchar(14) DEFAULT NULL AFTER `phone`;

-- Add index for faster searches
ALTER TABLE `inquiries`
ADD KEY `idx_ic_number` (`ic_number`);

-- Show the updated structure
DESCRIBE `inquiries`;
