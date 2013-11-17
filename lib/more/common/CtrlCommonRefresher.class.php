<?php

class CtrlCommonRefresher extends CtrlCommon {

  function action_default() {
    $this->d['mainTpl'] = 'clearTpl';
    if (isset($this->req->params[0])) {
      $this->d['tpl'] = 'common/refresher';
      $this->d['subTpl'] = 'refresher/'.$this->req->param(2);
    }
  }

}