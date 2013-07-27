<?php

q("ALTER TABLE  `tags_groups` ADD  `masterStrName` VARCHAR( 50 ) NOT NULL AFTER  `tree`");
q("ALTER TABLE  `dd_structures` CHANGE  `name`  `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");
q("ALTER TABLE  `dd_structures` CHANGE  `slaveStrName`  `slaveStrName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");