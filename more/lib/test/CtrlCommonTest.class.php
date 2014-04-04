<?php

class CtrlCommonTest extends CtrlCommon {

  function action_default() {
    print '<pre style="padding-left:20px">';
    (new TestRunnerAbstract())->_test($this->req->param(2));
  }

}