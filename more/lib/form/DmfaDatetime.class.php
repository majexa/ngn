<?php

class DmfaDatetime extends Dmfa {

  function form2sourceFormat($v) {
    if (!$v) return '0000-00-00 00:00:00';
    return sprintf("%04s-%02s-%02s %02s:%02s:00", $v[2], $v[1], $v[0], $v[3], $v[4]);
  }
  
  function source2formFormat($v) {
    if (!$v) return '';
    preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2})/', $v, $m);
    return [$m[3], $m[2], $m[1], $m[4], $m[5]];
  }

}