<?php

abstract class CtrlCommonPatcher extends CtrlCommon {
  
  protected $patcher;

  function init() {
    $this->d['patcher'] = $this->patcher = $this->getPatcher();
    $this->d['mainTpl'] = 'common/installer/patcher';
  }
  
  abstract protected function getPatcher();

  protected function action_patch() {
    sendHeader();
    $this->hasOutput = false;
    set_time_limit_q(0);
    $this->patcher->setLogger('prr');
    if (isset($this->req->r['n'])) {
      $this->patcher->make($this->req->r['n']);
    } else {
      $this->patcher->patch();
    }
    print '<p><a href="/">'.SITE_TITLE.'</a></p>';
  }
  
}
