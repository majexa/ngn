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

  static function runCode($server, $code, $includes, $runBasePath = null) {
    $code = self::formatRunCmd($code, $includes, $runBasePath);
    return sys("ssh $server $code");
  }

  static function ssh($server, $cmd) {
    return sys("ssh $server '$cmd'");
  }

  static function formatRunCmd($code, $includes, $runBasePath = null) {
    $code = str_replace("'", '"', $code);
    $code = str_replace('"', '\\"', $code);
    return "'".self::addRunPaths($code, $includes, $runBasePath)."'";
  }

  static function addRunPaths($code, $includes, $runBasePath = null) {
    return 'php '.($runBasePath ? $runBasePath : '~').'/ngn-env/run/run.php "'.$code.'" '.$includes;
  }

  static function rpc($server, $code) {
    $cmd = "ssh $server sudo -u user TERM=dumb 'php /home/user/ngn-env/run/run.php rpc \"$code\"'";
    return json_decode(`$cmd`, true);
  }

  static function shell($cmd, $output = true) {
    if ($output) output($cmd);
    return `$cmd`;
  }

}