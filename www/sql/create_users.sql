CREATE TABLE `users`(
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(1000) NOT NULL UNIQUE, 
	`password` VARCHAR(128) NOT NULL,
	`active` TINYINT NOT NULL,
	 PRIMARY KEY (id)
);

ALTER TABLE `users` ADD COLUMN (`priviledges` CHAR(1) DEFAULT 'R');

INSERT INTO `users` VALUES(1, 'iitr.sourabh@gmail.com', 'khushbu', '1', 'A');

ALTER TABLE `users` ADD COLUMN `name` VARCHAR(1024) NOT NULL DEFAULT 'Anonymous';

UPDATE users SET NAME = "Sourabh";

INSERT INTO `users` VALUES(2, 'coolmites@gmail.com', 'mitesh', '1', 'C', "Mitesh");
