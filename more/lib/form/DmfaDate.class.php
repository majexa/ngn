<?php

class DmfaDate extends Dmfa {

  function form2sourceFormat($v, $fieldName) {
    if (empty($v)) return '';
    if (is_string($v)) $v = explode('.', $v);
    return sprintf("%04s-%02s-%02s", $v[2], $v[1], $v[0]);
  }
  
  function source2formFormat($v) {
    if (!$v) return [0 => 0,  1 => 0, 2 => 0];
    $r = explode('-', $v);
    return [$r[2], $r[1], $r[0]];
  }

}