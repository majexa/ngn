<?php

class CtrlPageAuth extends CtrlCommon {

  function init() {
    $this->d['mainTpl'] = 'no-auth/main';
  }
  
  function action_ajax_invites() {
    $this->tt->tpl('no-auth/invadors');
  }

}
