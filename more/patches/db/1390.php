<?

q("ALTER TABLE `storeCart` CHANGE COLUMN `itemId` `cartId` INT(11) NOT NULL DEFAULT '0' AFTER `pageId`;");
q("ALTER TABLE `storeCart` CHANGE COLUMN `cartId` `cartId` VARCHAR(100) NOT NULL DEFAULT '0' AFTER `pageId`;");