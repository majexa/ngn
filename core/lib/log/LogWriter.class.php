<?php

class LogWriter {

  static $output = false;

  /**
   * @param string $name Имя лога
   * @param string $var Строка, которую нужно записать в лог-файл
   * @param array|null $trace Трассировка
   * @param array $params Дополнительные параметры
   */
  static function v($name, $var, array $trace = null, array $params = []) {
    if (getConstant('DO_NOT_LOG')) return;
    $str = var_export($var, true);
    if (self::$output) print "\n$str";
    self::html($name, "<pre>$str</pre>", $trace, $params);
  }

  /**
   * Записывает HTML в лог-файл. Лог-файл поддерживается LogReader'ом
   *
   * @param string $name Имя лога
   * @param string $html Строка, которую нужно записать в лог-файл
   * @param array|null $trace Трассировка
   * @param array $params Дополнительные параметры
   * @param bool $force
   */
  static function html($name, $html, array $trace = null, array $params = [], $force = false) {
    $r = [
      'file' => __FILE__.':'.__LINE__,
    ];
    if (!getConstant('CLI') and isset($_SERVER['REQUEST_URI'])) {
      $r['url'] = (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$_SERVER['REQUEST_URI'];
      $r['referer'] = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }
    if ($params) $r['params'] = $params;
    $r['body'] = $html;
    $r['trace'] = $trace ? _getBacktrace($trace, false) : getBacktrace(false);
    if (!empty($_POST)) {
      $r['post'] = $_POST;
    }
    $r['time'] = time();
    self::str('r_'.$name, json_encode($r), null, $force, true);
    self::str('h_'.$name, print_r($r, true), null, $force, true);
  }

  /**
   * Записывает строку в лог-файл. Лог-файл не поддерживается LogReader'ом
   *
   * @param string $name Имя лога
   * @param string $str Строка, которую нужно записать в лог-файл
   * @param string|null $logsPath Путь к папке с логами
   * @param bool $force Записывать лог в любом случае независимо от флага DO_NOT_LOG
   * @param bool $disableTime Выключить запись даты/времени
   */
  static function str($name, $str, $logsPath = null, $force = false, $disableTime = false) {
    if (!$force and getConstant('DO_NOT_LOG')) return;
    if (!defined('LOGS_PATH') and !$logsPath) return;
    if (self::$output) print "$str\n";
    $str = $disableTime ? $str."\n" : date('d.m.Y H:i:s').": $str\n";
    $dir = $logsPath ? $logsPath : LOGS_PATH;
    if (!is_dir($dir)) die("Error: Logs dir '$dir' does not exists. Define LOGS_PATH constant");
    $exists = file_exists("$dir/$name.log");
    file_put_contents("$dir/$name.log", $str, FILE_APPEND);
    if (!$exists) `sudo chmod 0666 $dir/$name.log`;
  }

}