<?php

class CtrlCommonCommonAuthSubs extends CtrlCommonAuth {
  
  function action_default() {
    $this->d['tpl'] = 'common/auth-subs-ajax';
  }  
  
}
