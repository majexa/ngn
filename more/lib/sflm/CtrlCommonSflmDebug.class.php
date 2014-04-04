<?php

class CtrlCommonSflmDebug extends CtrlCammon {

  function action_default() {
    Sflm::setFrontend('default');
    die2(Sflm::frontend('js')->getPaths());
  }

}