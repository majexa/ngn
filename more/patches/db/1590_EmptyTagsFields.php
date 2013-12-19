<?php

foreach (db()->selectCol('SELECT name FROM dd_structures') as $strName) {
  $m = new DdFields($strName);
  foreach (db()->cols("dd_i_$strName") as $name){
    if(DdTags::isTagType($m->getType($name)) !== false) db()->query("UPDATE dd_i_$strName SET $name = '' ");
  }
}