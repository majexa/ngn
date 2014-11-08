<?php

class Cli {

  static function storeCommand($dir) {
    $argv = $_SERVER['argv'];
    $argv[0] = str_replace('.php', '', $argv[0]);
    LogWriter::str('commands', implode(' ', $argv), $dir);
  }

  static function filePutCommand($cmd, $file, $append = false) {
    return "'( cat << EOF\n$cmd\nEOF\n) ".($append ? '>>' : '>')." $file'";
  }

  static function formatRunCmd($code, $includes, $runBasePath = null) {
    $code = str_replace("'", '"', $code);
    return "'".self::addRunPaths($code, $includes, $runBasePath)."'";
  }

  static function addRunPaths($code, $includes, $runBasePath = null) {
    return 'php '.($runBasePath ? $runBasePath : '~').'/ngn-env/run/run.php "'.$code.'" '.$includes;
  }

  //static function rpc($server, $code) {
  //  $cmd = "ssh $server sudo -u user TERM=dumb 'php /home/user/ngn-env/run/run.php rpc \"$code\"'";
  //  return json_decode(`$cmd`, true);
  //}

  static function shell($cmd, $output = true) {
    if ($output) output($cmd);
    return `$cmd`;
  }

  static function confirm($text) {
    print "$text\nEnter 'y' if agree.\n";
    $fp = fopen('php://stdin', 'r');
    $lastLine = false;
    while (!$lastLine) {
      $nextLine = fgets($fp, 1024);
      return 'y' == lcfirst(trim($nextLine)) ? true : false;
    }
  }

  static function prompt($caption = null) {
    print ($caption ? : "Enter text").":\n";
    $fp = fopen('php://stdin', 'r');
    $nextLine = false;
    while (!$nextLine) {
      $nextLine = fgets($fp, 1024);
      if ($nextLine[strlen($nextLine) - 1] == "\n") break;
    }
    $nextLine = trim($nextLine);
    return $nextLine;
  }

  static function replaceOut($str) {
    $numNewLines = substr_count($str, "\n");
    echo chr(27)."[0G"; // Set cursor to first column
    echo $str;
    echo chr(27)."[".$numNewLines."A"; // Set cursor up x lines
  }

  static function columns($columns, $highlightFirstRow = false) {
    if (!count($columns)) return '';
    $width = [];
    for ($i = 0; $i < count($columns); $i++) $width[$i] = 0;
    for ($i = 0; $i < count($columns); $i++) {
      foreach ($columns[$i] as $word) if (strlen($word) > $width[$i]) $width[$i] = strlen($word);
    }
    $n = 0;
    $rowIsEmpty = function ($n) use ($columns) {
      for ($i = 0; $i < count($columns); $i++) if (isset($columns[$i][$n])) return false;
      return true;
    };
    $r = '';
    while (true) {
      if ($rowIsEmpty($n)) break;
      for ($i = 0; $i < count($columns); $i++) {
        $s = str_pad(isset($columns[$i][$n]) ? $columns[$i][$n] : '', $width[$i] + 5);
        if ($highlightFirstRow and $n == 0) $s = O::get('CliColors')->getColoredString($s, 'brown');
        $r .= $s;
      }
      $r .= "\n";
      $n++;
    }
    return $r;
  }

  static function strParamsToArray($s) {
    $options = [];
    if (strstr($s, '=')) {
      $argv = str_replace('+', '&', $s);
      parse_str($argv, $options);
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

  static function realTimeCmd($cmd) {
    $descriptorspec = array(
      0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
      1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
      2 => array("pipe", "w")    // stderr is a pipe that the child will write to
    );
    //flush();
    $process = proc_open($cmd, $descriptorspec, $pipes, realpath('./'), array());
    if (is_resource($process)) {
      while ($s = fgets($pipes[1])) {
        print $s;
        //flush();
      }
    }
  }

}