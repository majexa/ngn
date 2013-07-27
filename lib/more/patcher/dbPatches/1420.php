<?php

q("ALTER TABLE  `dd_structures` CHANGE  `slaveStrName`  `filterStrName` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL");

q("ALTER TABLE  `dd_structures` CHANGE  `dateUpdate`  `dateUpdate` DATETIME NOT NULL");



