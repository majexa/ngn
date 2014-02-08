<?php

class CliHelpMultiWrapper extends ArrayAccessebleOptions {

  function action($method) {
    if (method_exists($this, $method)) {
      $this->$method();
      return;
    }
    $singleClass = Misc::removeSuffix('s', get_class($this));
    foreach ($this->records as $v) {
      $this->options['name'] = $v['name'];
      (new $singleClass($this->options))->$method();
    }
  }

}