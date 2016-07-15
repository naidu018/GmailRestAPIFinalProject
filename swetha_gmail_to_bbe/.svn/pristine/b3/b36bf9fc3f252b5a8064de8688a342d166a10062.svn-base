<?php /*



////////////////////////////////////////////
// DONE!!!!
////////////////////////////////////////////
ALTER TABLE `nm_paper`.`show_mails` 
	ADD COLUMN `assign_psetup` VARCHAR(255) NULL  AFTER `log` , 
	ADD COLUMN `assign_paper_name` VARCHAR(255) NULL  AFTER `assign_psetup` , 
	ADD COLUMN `assign_psetup_time` INT(10) NULL  AFTER `assign_paper_name` ;


ALTER TABLE `nm_paper`.`show_mails` ADD COLUMN `deadline` INT(10) NULL  AFTER `assign_psetup_time` ;

ALTER TABLE `nm_paper`.`show_mails` 
	CHANGE COLUMN `assign_psetup` `assign_psetup` TEXT NULL DEFAULT NULL,
	CHANGE COLUMN `assign_paper_name` `assign_paper_name` TEXT NULL DEFAULT NULL  ;


ALTER TABLE `nm_paper`.`show_mails` ADD COLUMN `deadline_type` TINYINT(3) UNSIGNED NULL  AFTER `deadline` ;


ALTER TABLE `nm_paper`.`show_mails` 
ADD INDEX `time_read_index` (`time` ASC, `time_read` ASC) ;

ALTER TABLE `nm_paper`.`show_mails`
	ADD COLUMN `parent_id` INT(15) UNSIGNED NULL  AFTER `deadline_type` ;

