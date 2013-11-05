<?php

foreach (DdCore::tables() as $table) {
  q("ALTER TABLE $table ADD COLUMN oid INT(11) NOT NULL DEFAULT '0' AFTER id");
}