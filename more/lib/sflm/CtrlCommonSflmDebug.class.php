<?php

class CtrlCommonSflmDebug extends CtrlCammon {

  function action_default() {
    Sflm::setFrontendName('default');
    die2(Sflm::frontend('js')->getPaths());
  }

}