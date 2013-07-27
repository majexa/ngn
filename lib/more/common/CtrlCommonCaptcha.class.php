<?php

class CtrlCommonCaptcha extends CtrlCammon {

  function action_ajax_check() {
    if (isset($_SESSION['captcha_keystring']) and $_SESSION['captcha_keystring'] == $this->req->rq('keystring')) {
      $this->ajaxSuccess = true;
    } else {
      $_SESSION['captcha_keystring'] = 'wrong';
      $this->ajaxSuccess = false;
    }
  }

  function action_captcha() {
    $this->hasOutput = false;
    $_SESSION['captcha_keystring'] = (new Kcaptcha)->getKeyString();
  }

}
