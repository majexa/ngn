<?php

q("ALTER TABLE `upload_temp` ADD COLUMN `multiple` TINYINT(1) NOT NULL DEFAULT '0' AFTER `name`");
q("ALTER TABLE  `upload_temp` ADD  `dateCreate` DATETIME NOT NULL AFTER  `multiple`");