<?php

class CtrlCommonJsRedirect extends CtrlBlank {

  protected function init() {
    $this->d['mainTpl'] = 'admin/main';
  }
  
  function action_default() {
    $this->d['redirect'] = $this->req->rq('r');
    $this->d['tpl'] = 'common/jsRedirect';
  }
  
}
