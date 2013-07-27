<?php

class ProcessTime {
  
  static function start() {
    R::set('processTimeStart', getMicrotime());
  }
  
  static function end() {
    return round(getMicrotime() - R::get('processTimeStart'), 3);
  }
  
}
