CREATE TABLE `articles_tags`(
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`article_id` BIGINT(20) NOT NULL,
	`tag_id` BIGINT(20) NOT NULL, 
	 PRIMARY KEY (id)
);