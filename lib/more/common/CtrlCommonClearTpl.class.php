<?php

class CtrlCommonClearTpl extends CtrlCommon {

  protected function init() {
    $this->hasOutput = false;
  }
  
  function action_default() {
    $this->tt->tpl('clearTpl', ['tpl' => 'clearTpl/'.$this->req->param(2)]);
  }
  
  function action_json_asd() {
    return new Form(new Fields([
      [
        'title' => 'dqqdw',
        'type' => 'wisiwigSimple',
        'name' => 'asd',
      ]
    ]));
  }
  
}
