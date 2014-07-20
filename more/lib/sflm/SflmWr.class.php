<?php

class SflmWr {

  static function __callStatic($name, $arguments) {
    LogWriter::str('sflm_call', $name.'::'.implode(', ', $arguments));
    forward_static_call_array(['Sflm_', $name], $arguments);
  }

}
