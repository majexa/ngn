<?php

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__), RecursiveIteratorIterator::SELF_FIRST);
foreach ($objects as $name => $object) {
  if (is_dir($object)) continue;
  if (strstr($object, '.git')) continue;
  if (strstr($object, '.jpg')) continue;
  if (strstr($object, '.png')) continue;
  if (strstr($object, 'cache')) continue;
  $c = file_get_contents($object);
  if (strstr($c, "\r\n")) {
    echo "$name\n";
    file_put_contents($object, str_replace("\r\n", "\n", $c));
  }
