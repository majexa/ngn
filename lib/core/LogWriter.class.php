<?php

class LogWriter {

  static $output = false;

  /**
   * Записывает "var_export" в лог-файл. Лог-файл поддерживается LogReader'ом
   *
   * @param   string  Имя лога
   * @param   string  Строка, которую нужно записать в лог-файл
   * @param   array   Дополнительные параметры
   */
  static function v($name, $var, array $trace = [], array $params = []) {
    $str = var_export($var, true);
    if (self::$output) print "\n$str";
    self::html($name, "<pre>$str</pre>", $trace, $params);
  }

  /**
   * Записывает HTML в лог-файл. Лог-файл поддерживается LogReader'ом
   *
   * @param   string  Имя лога
   * @param   string  Строка, которую нужно записать в лог-файл
   * @param   array   Дополнительные параметры
   */
  static function html($name, $html, array $trace = [], array $params = []) {
    $s = '('.__FILE__.':'.__LINE__.")\n";
    if (isset($_SERVER['REQUEST_URI'])) {
      $s .= 'url: '.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$_SERVER['REQUEST_URI'].', referer: '.(!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
      if ($params) $s .= ', ';
    }
    if ($params) $s .= Tt()->enum($params, ', ', '$k.`: `.$v');
    $s .= "\n";
    $s .= "<body>".$html."</body>";
    $html = false;
    $s .= "\n<trace>".($trace ? _getBacktrace($trace, $html) : getBacktrace($html))."</trace>";
    if (!empty($_POST)) {
      $s .= "\n<post>".print_r($_POST, true)."</post>";
    }
    $s .= "\n=====+=====\n";
    self::str('r_'.$name, $s);
  }

  /**
   * Записывает строку в лог-файл. Лог-файл не поддерживается LogReader'ом
   *
   * @param   string  Имя лога
   * @param   string  Строка, которую нужно записать в лог-файл
   */
  static function str($name, $str, $logsPath = null) {
    if (getConstant('DO_NOT_LOG')) return;
    if (!defined('LOGS_PATH')) return;
    if (self::$output) print "$str\n";
    $str = date('d.m.Y H:i:s').": $str\n";
    $dir = $logsPath ? $logsPath : LOGS_PATH;
    if (!is_dir($dir)) die("Error: Logs dir '$dir' does not exists. Define LOGS_PATH constant");
    $exists = file_exists("$dir/$name.log");
    file_put_contents("$dir/$name.log", $str, FILE_APPEND);
    if (!$exists) `sudo chmod 0666 $dir/$name.log`;
  }

}