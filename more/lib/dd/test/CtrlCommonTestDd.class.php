<?php

class CtrlCommonTestDd extends CtrlCammon {

  protected function init() {
    $this->d['mainTpl'] = 'clearTpl';
  }

  function action_dialogForm() {
    Sflm::frontend('js')->addClass('Ngn.Dialog.RequestForm');
    $this->d['tpl'] = 'test/ddFormDialog';
  }

  function action_json_dialogForm() {
    return new DdForm(new DdFields('a'), 'a');
  }

}