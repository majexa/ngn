<?php

class CtrlCommonJsRedirect extends CtrlBlank {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }
  
  function action_default() {
    Sflm::flm('js')->addClass('MooCountdown');
    $this->d['redirect'] = $this->req->rq('r');
    $this->d['tpl'] = 'common/jsRedirect';
  }
  
}
