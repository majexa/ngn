<?php

class LogReader {

  static function get($name) {
    return self::_get(LOGS_PATH.'/r_'.$name.'.log');
  }

  static function parse($name) {
    $r = self::get($name);
    foreach ($r as &$v) $v['body'] = self::parseBody($v['body']);
    return $r;
  }

  static protected function parseBody($body) {
    if (!preg_match('/<pre>(.*)<\/pre>/sm', $body, $m)) throw new Exception('no pre in body');
    return eval("return {$m[1]};");
  }

  /**
   * Парсит лог-файл и возвращает массив
   *
   * @param string $file Имя лог-файла
   * @return Generator
   */
  static function _get($file) {
    $a = function($file) {
      $handle = fopen($file, 'r');
      if ($handle) {
        $c = '';
        while (($buffer = fgets($handle, 4096)) !== false) {
          $c .= $buffer;
          if (strstr($c, '=====+=====')) {
            $p = explode('=====+=====', $c)[0];
            $c = '';
            yield $p;
          }
        }
        fclose($handle);
      }
    };
    $b = function() use ($file, $a) {
      foreach ($a($file) as $p) {
        if (!preg_match('/(\d+.\d+.\d+ \d+.\d+.\d+): \((.*)\)\n(.*)\n<body>(.*)<\/body>\n<trace>(.*)<\/trace>/ms', $p, $m)) continue;
        $v = [];
        $v['time'] = strtotime($m[1]);
        $v['body'] = str_replace("\\'", '', $m[4]);
        $v['file'] = $file;
        $v['trace'] = $m[5];
        if ($m[3]) {
          $params = explode(', ', $m[3]);
          foreach ($params as $param) {
            $r = explode(': ', $param);
            $v[$r[0]] = $r[1];
          }
        }
        if (preg_match('/<post>(.*)<\/post>/ms', $p, $m)) $v['post'] = $m[1];
        yield $v;
      }
    };
    return $b();
  }

  /**/

  static function logs() {
    $logs = [];
    foreach (glob(LOGS_PATH.'/r_*') as $v) {
      $logs[] = preg_replace('/^r_(.*).log/', '$1', basename($v));
    }
    return $logs;
  }

  static function delete($name) {
    unlink(LOGS_PATH.'/r_'.$name.'.log');
  }

  static function cleanup($name) {
    file_put_contents(LOGS_PATH.'/r_'.$name.'.log', '');
  }

}