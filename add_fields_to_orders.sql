-- Add missing fields to orders table for delivery date and cash on delivery
ALTER TABLE `orders` 
ADD COLUMN `delivery_date` DATE NULL DEFAULT NULL AFTER `created_at`,
ADD COLUMN `cash_on_delivery` TINYINT(1) DEFAULT 0 AFTER `payment_method`,
ADD COLUMN `cod_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `cash_on_delivery`;