<?php

class CtrlCommonClearTpl extends CtrlBase {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }

  function action_default() {
    $this->d['tpl'] = 'clearTpl/'.$this->req->path(2);
  }

}
