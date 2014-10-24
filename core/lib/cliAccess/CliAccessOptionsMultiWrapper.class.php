<?php

abstract class CliAccessOptionsMultiWrapper extends ArrayAccessebleOptions {

  /**
   * @return array
   */
  abstract protected function records();

  function action($method) {
    if (method_exists($this, $method)) $this->$method();
    $singleClass = rtrim(get_class($this), 's');
    if (method_exists($singleClass, $method)) {
      foreach ($this->records() as $v) {
        $this->options['name'] = $v['name'];
        (new $singleClass($this->options))->$method();
      }
    }
  }

}