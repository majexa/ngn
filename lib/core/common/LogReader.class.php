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
   * @param   string  Имя лог-файла
   * @return  array
   */
  static function _get($file) {
    if (!file_exists($file)) return [];
    $r = [];
    foreach (explode('=====+=====', file_get_contents($file)) as $v) {
      if (!preg_match('/(\d+.\d+.\d+ \d+.\d+.\d+): \((.*)\)\n(.*)\n<body>(.*)<\/body>\n<trace>(.*)<\/trace>/ms', $v, $m)) continue;
      $i['time'] = strtotime($m[1]);
      $i['body'] = str_replace("\\'", '', $m[4]);
      $i['file'] = $file;
      $i['trace'] = $m[5];
      if ($m[3]) {
        $params = explode(', ', $m[3]);
        foreach ($params as $param) {
          $p = explode(': ', $param);
          $i[$p[0]] = $p[1];
        }
      }
      if (preg_match('/<post>(.*)<\/post>/ms', $v, $m)) $i['post'] = $m[1];
      $r[] = $i;
    }
    return empty($r) ? [] : Arr::sortByOrderKey($r, 'time', SORT_DESC);
  }

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