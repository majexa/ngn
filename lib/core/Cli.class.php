<?php

class Cli {

  static function storeCommand($dir) {
    $argv = $_SERVER['argv'];
    $argv[0] = str_replace('.php', '', $argv[0]);
    LogWriter::str('commands', implode(' ', $argv), $dir);
  }

  static function formatPutFileCommand($cmd, $file, $append = false) {
    return "'( cat << EOF\n$cmd\nEOF\n) ".($append ? '>>' : '>')." $file'";
  }

  static function runCode($server, $code, $includes) {
    $code = self::formatRunCmd($code, $includes);
    sys("ssh $server '$code'");
  }

  static function formatRunCmd($code, $includes) {
    $code = str_replace("'", '"', $code);
    $code = str_replace('"', '\\"', $code);
    return '\'php ~/run/run.php "'.$code.'" '.$includes.'\'';
  }

}