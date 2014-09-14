<?php

abstract class SubCtrl {
  
  /**
   * @var CtrlCommon
   */
  protected $ctrl;
  
  public $disable = false;
  
  function __construct(CtrlCommon $ctrl) {
    $this->ctrl = $ctrl;
  }
  
  function getName() {
    return lcfirst(Misc::removePrefix('SubPa', get_called_class()));
  }
  
  function init() {}

}
