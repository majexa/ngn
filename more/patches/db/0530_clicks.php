<?php

foreach (DdCore::tables() as $table) {
  db()->query("UPDATE $table SET clicks=0");
}
