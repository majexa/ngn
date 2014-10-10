<?php

class CtrlCommonJsRedirect extends CtrlCommon {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }
  
  function action_default() {
    $this->d['redirect'] = $this->req->rq('r');
    $this->d['tpl'] = 'common/jsRedirect';
  }
  
}
