<?php

class CtrlCommonRedirect extends CtrlCommon {

  function action_default() {
    RedirectRecord::save($this->req->params[2], $this->req->rq('url'));
    $this->redirect($this->req->rq('url'));
  }
  
}
