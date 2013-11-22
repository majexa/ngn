<?php

class CtrlCommonClearTpl extends CtrlCommon {

  function action_default() {
    $this->d['mainTpl'] = 'clearTpl';
    $this->d['tpl'] = 'clearTpl/'.$this->req->path(2);
  }

}
