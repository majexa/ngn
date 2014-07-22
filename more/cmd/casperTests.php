<?php

foreach (Ngn::$basePaths as $path) {
  foreach (Dir::getFilesR($path, '*.test.json') as $file) {
    $tests[] = file_get_contents($file);
  }
}

if (isset($tests)) print "[\n".implode(",\n", $tests)."\n]\n";