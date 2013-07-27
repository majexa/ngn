<?php

class CtrlPageTpl extends CtrlPage {

  public $requiredSettings = ['tplName'];

  function action_default() {
    $this->d['tpl'] = 'tpl/'.($this->page['settings']['tplName'] ? $this->page['settings']['tplName'] : $this->page['name']);
  }

}