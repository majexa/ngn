<?php

class CtrlCommonTestDd extends CtrlCammon {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }

  function action_dialogForm() {
    Sflm::frontend('js')->addObject('Ngn.Dialog.RequestForm');
    $this->d['tpl'] = 'test/ddFormDialog';
  }

  function action_json_dialogForm() {
    $this->jsonFormAction(new DdForm(new DdFields('a'), 'a'));
  }

}