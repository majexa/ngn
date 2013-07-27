<?php

foreach (DdCore::tables() as $table)
  q("ALTER TABLE $table ADD INDEX (pageId)");
