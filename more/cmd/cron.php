<?php

foreach (Ngn::$basePaths as $basePath) {
  if (file_exists($basePath.'/.cron')) {
    $c = trim(file_get_contents($basePath.'/.cron'));
    $c = str_replace('{cmd}', 'run site '.PROJECT_KEY, $c);
    print $c."\n";
  }
}