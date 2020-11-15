DROP TABLE IF EXISTS `account_role`;
CREATE TABLE `account_role` (`id` int unsigned AUTO_INCREMENT PRIMARY KEY) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `account_role` ADD `name` varchar(255) CHARACTER SET utf8;
ALTER TABLE `account_role` ADD `active` varchar(1) default 1;

INSERT INTO account_role VALUES('1','SuperAdmin','0');
INSERT INTO account_role VALUES('2','Admin','1');
INSERT INTO account_role VALUES('3','Moderator','1');
INSERT INTO account_role VALUES('5','Empoyee','1');
INSERT INTO account_role VALUES('6','Empoyer','1');



