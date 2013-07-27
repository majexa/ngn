<?php

foreach (DdCore::tables() as $v) {
  
}

if (file_exists(UPLOAD_PATH.'/editor'))
  rename(UPLOAD_PATH.'/editor', UPLOAD_PATH.'/ed');