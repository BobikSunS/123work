-- SQL script to ensure recipient fields exist in orders table
-- This script checks for the existence of recipient fields and adds them if they don't exist

-- First, check if recipient_name column exists, if not add it
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'delivery_by' 
                   AND TABLE_NAME = 'orders' 
                   AND COLUMN_NAME = 'recipient_name');

SET @sql = IF(@col_exists = 0, 
              'ALTER TABLE orders ADD COLUMN recipient_name VARCHAR(255) DEFAULT NULL', 
              'SELECT "recipient_name column already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Then check if recipient_address column exists, if not add it
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'delivery_by' 
                   AND TABLE_NAME = 'orders' 
                   AND COLUMN_NAME = 'recipient_address');

SET @sql = IF(@col_exists = 0, 
              'ALTER TABLE orders ADD COLUMN recipient_address TEXT DEFAULT NULL', 
              'SELECT "recipient_address column already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Also add other common fields that may be missing
SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'delivery_by' 
                   AND TABLE_NAME = 'orders' 
                   AND COLUMN_NAME = 'full_name');

SET @sql = IF(@col_exists = 0, 
              'ALTER TABLE orders ADD COLUMN full_name VARCHAR(255) DEFAULT NULL', 
              'SELECT "full_name column already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = (SELECT COUNT(*) 
                   FROM INFORMATION_SCHEMA.COLUMNS 
                   WHERE TABLE_SCHEMA = 'delivery_by' 
                   AND TABLE_NAME = 'orders' 
                   AND COLUMN_NAME = 'home_address');

SET @sql = IF(@col_exists = 0, 
              'ALTER TABLE orders ADD COLUMN home_address TEXT DEFAULT NULL', 
              'SELECT "home_address column already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update any existing orders that might have missing recipient info by pulling from related fields
UPDATE orders 
SET recipient_name = delivery_city 
WHERE recipient_name IS NULL AND delivery_city IS NOT NULL AND delivery_city != '';

UPDATE orders 
SET recipient_address = delivery_address 
WHERE recipient_address IS NULL AND delivery_address IS NOT NULL AND delivery_address != '';

SELECT 'Database recipient fields setup completed' as message;