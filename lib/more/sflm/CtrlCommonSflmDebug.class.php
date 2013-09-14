<?php

class CtrlCommonSflmDebug extends CtrlCammon {

  function action_default() {
    Sflm::$frontend = 'default';
    die2(Sflm::flm('js')->getPaths());
  }

}