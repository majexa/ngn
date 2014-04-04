<?php

abstract class ObjectCacher {

  protected $object, $cache = [];

  protected function _getObject() {
    if (isset($this->object)) return $this->object;
    return $this->object = $this->getObject();
  }

  abstract protected function getObject();

  function __call($method, $args) {
    $object = $this->_getObject();
    ClassCore::checkExistance($object, $method);
    if (isset($this->cleanupMethods()[$method])) {
      foreach ($this->cleanupMethods()[$method] as $cleanMethod) unset($this->cache[$cleanMethod]);
    }
    if (in_array($method, $this->cachedMethods())) {
      $r = call_user_func_array([$object, $method], $args);
      $this->cache[$method] = $r;
      return $r;
    }
    return call_user_func_array([$object, $method], $args);
  }

  abstract function cachedMethods();

  protected function cleanupMethods() {
    return [];
  }

}