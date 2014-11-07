<?php

abstract class CliAccessOptionsMultiWrapper extends ArrayAccessebleOptions {

  /**
   * @return array
   */
  abstract protected function records();

  protected $records;

  protected function _records() {
    if (isset($this->records)) return $this->records;
    return $this->records = $this->records();
  }

  function action($method) {
    if (method_exists($this, $method)) $this->$method();
    $singleClass = rtrim(get_class($this), 's');
    if (method_exists($singleClass, $method)) {
      $this->beforeActions();
      foreach ($this->_records() as $v) {
        $this->options['name'] = $v['name'];
        $this->beforeAction();
        (new $singleClass($this->options))->$method();
        $this->afterAction();
      }
      $this->afterActions();
    }
  }

  protected function beforeActions() {}
  protected function afterActions() {}
  protected function beforeAction() {}
  protected function afterAction() {}

}