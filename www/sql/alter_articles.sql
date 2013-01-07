ALTER TABLE `articles` ADD COLUMN `created_by` VARCHAR(1000) NOT NULL DEFAULT 'iitr.sourabh@gmail.com';
ALTER TABLE `articles` ADD COLUMN `last_modified_by` VARCHAR(1000) NOT NULL DEFAULT 'iitr.sourabh@gmail.com'
