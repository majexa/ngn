<?php

class Js {

  static function gridBody(array $rows, $callback) {
    $rows = array_values($rows);
    return array_map(function($v) use ($callback) {
      $r = $callback($v);
      $r['id'] = $v['id'];
      Arr::checkEmpty($r, 'data');
      return $r;
    }, $rows);
  }

}