-- Add export flag to inquiries table
-- This allows tracking which inquiries have been exported via API

-- Add is_exported column
ALTER TABLE `inquiries` 
ADD COLUMN `is_exported` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0=Not exported, 1=Exported' AFTER `status`,
ADD COLUMN `exported_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the inquiry was exported' AFTER `is_exported`;

-- Add index for better query performance
ALTER TABLE `inquiries` 
ADD INDEX `idx_is_exported` (`is_exported`);

-- Optional: Add index for exported_at for time-based queries
ALTER TABLE `inquiries` 
ADD INDEX `idx_exported_at` (`exported_at`);
