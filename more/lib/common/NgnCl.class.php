<?php

/**
 * Command Line
 */
class NgnCl {

  static function strParamsToArray($s) {
    $options = [];
    if (strstr($s, '=')) {
      $argv3 = str_replace('+', '&', $s);
      parse_str($argv3, $options);
      foreach ($options as &$v) $v = Arr::formatValue2($v);
    }
    return $options;
  }

  static function arrayToStrParams(array $a) {
    $r = [];
    foreach ($a as $k => $v) {
      $r[] = $k.'='.$v;
    }
    return implode('+', $r);
  }

  static function parseArgv(array $argv, array &$options) {
    foreach ($argv as $arg) {
      if (substr($arg, 0, 2) == '--' and isset($options[substr($arg, 2)])) {
        $options[substr($arg, 2)] = true;
      }
    }
  }

  static function slowCmd($id, $cmd) {
    Dir::make(TEMP_PATH.'/slowCmd');
    //File::checkFilename($id);
    if (self::slowCmdIsRunning($id)) throw new Exception("Slow command ID='$id' already launched");
    file_put_contents(TEMP_PATH."/slowCmd/$id", time()."\n".$cmd);
    sys($cmd);
    unlink(TEMP_PATH."/slowCmd/$id");
  }

  static function slowCmdIsRunning($id) {
    return file_exists(TEMP_PATH."/slowCmd/$id");
  }

}
