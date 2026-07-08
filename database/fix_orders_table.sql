ALTER TABLE `orders`
    CHANGE COLUMN `total_price` `total_amount` DECIMAL(12,2) NOT NULL,
    ADD COLUMN `receiver_name` VARCHAR(255) DEFAULT NULL AFTER `note`,
    ADD COLUMN `receiver_phone` VARCHAR(20) DEFAULT NULL AFTER `receiver_name`,
    ADD COLUMN `receiver_address` TEXT DEFAULT NULL AFTER `receiver_phone`;
