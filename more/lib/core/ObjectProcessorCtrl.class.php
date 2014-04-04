<?php

trait ObjectProcessorCtrl {

  /*
  protected function objectProcess($object, $suffix) {
    for ($i=0; $i<=3; $i++) {
      $method = 'process'.ucfirst($suffix).($i ? $i : '');
      if (method_exists($this, $method)) $this->$method($object);
    }
    return $object;
  }
  */

  protected function objectProcess($object, $suffix) {
    $prefix = 'process'.ucfirst($suffix);
    foreach (get_class_methods($this) as $method) {
      if (Misc::hasPrefix($prefix, $method)) {
        $this->$method($object);
      }
    }
    return $object;
  }

}