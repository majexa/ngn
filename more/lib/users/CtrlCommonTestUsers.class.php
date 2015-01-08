<?php

class CtrlCommonTestUsers extends CtrlCammon {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }

  function action_dialogAuth() {
    $this->d['tpl'] = Auth::get('id') ? 'test/success' : 'test/authDialog';
  }

}