<?php

q("ALTER TABLE `dd_fields` CHANGE `descr` `help` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL");
