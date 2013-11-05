<?php

foreach (DdCore::tables() as $t)
  q("ALTER TABLE $t ADD COLUMN userGroupId INT(11) NULL DEFAULT NULL AFTER userId;");
