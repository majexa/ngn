<?php

class CtrlCommonTestUsers extends CtrlCammon {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }

  function action_dialogAuth() {
    Sflm::frontend('js')->addObject('Ngn.Dialog.Auth');
    $this->d['tpl'] = 'test/authDialog';
  }

}