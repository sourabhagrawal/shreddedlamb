
ALTER TABLE categories ADD COLUMN `text` VARCHAR(100) NOT NULL DEFAULT '';

ALTER TABLE categories ADD COLUMN `order` INT NOT NULL DEFAULT 0;