<?php

abstract class ObjectMapper {

  abstract protected function getObject();

  function __call($method, $args) {
    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], $args);
    } else {
      return call_user_func_array([$this->getObject(), $method], $args);
    }
  }

}