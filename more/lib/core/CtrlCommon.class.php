<?php

abstract class CtrlCommon extends CtrlBase {

  /*
  function dispatch() {
    parent::dispatch();
    $this->sflmStore();
    return $this;
  }

  protected function sflmStore() {
    if (!empty($this->req->options['disableSflmStore'])) return;
    Sflm::frontend('js')->store('afterAction');
    Sflm::frontend('css')->store('afterAction');
  }
  */

}
