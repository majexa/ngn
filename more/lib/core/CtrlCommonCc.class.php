<?php

class CtrlCommonCc extends CtrlCommon {

  function action_ajax_sf() {
    Sflm::clearCache();
  }

}
