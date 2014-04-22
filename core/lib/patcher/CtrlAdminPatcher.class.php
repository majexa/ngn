<?php

class CtrlAdminPatcher extends CtrlAdmin {

  static $properties = [
    'title' => 'Патчи',
    'onMenu' => false
  ];
  
  function init() {
    $this->d['patches'] = O::get('DbPatcher')->getPatches();
    $this->d['tpl'] = 'patcher/default';
  }
  
  function action_make() {
    $this->d['result'] = O::get('DbPatcher')->make($this->req->r['patchN']);
    $this->d['tpl'] = 'patcher/make';
  }
  
  function action_patch() {
    O::get('DbPatcher')->patch();
    $this->redirect($this->tt->getPath(2));
  }

}
