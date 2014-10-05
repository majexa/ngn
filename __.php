<?php

$objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__), RecursiveIteratorIterator::SELF_FIRST);
foreach ($objects as $name => $object) {
  if (is_dir($object)) continue;
  if (!strstr($object, '.php')) continue;
  $c = file_get_contents($object);
  if (preg_match("/(?<!')LANG_[A-Za-z_]+/", $c)) {
    $c = preg_replace_callback("/(?<!')LANG_([A-Za-z_]+)/", function($m) {
      return "Lang::get('".Misc::camelCase(strtolower($m[1]), '_')."')";
    }, $c);
    $f = __DIR__.'/ttt/temp.php';
    file_put_contents($object, $c);
    //file_put_contents($f, $c);
    //print $name.' ... ';
    //print `php -l $f`;
    //die2(2);
    //$correct = php_check_syntax(__DIR__.'/ttt/temp.php');
    //if (!$correct) echo "=( $name\n";
    //file_put_contents($object, str_replace("\r\n", "\n", $c));
  }
}