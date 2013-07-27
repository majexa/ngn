<?php

q("ALTER TABLE `upload_temp` ADD COLUMN `formId` VARCHAR(255) NOT NULL AFTER `tempId`");
q("ALTER TABLE `upload_temp` ADD COLUMN `dateCreate` DATETIME NOT NULL AFTER `multiple`");