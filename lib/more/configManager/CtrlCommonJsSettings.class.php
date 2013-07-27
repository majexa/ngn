<?php

class CtrlCommonJsSettings extends CtrlCommon {

  function action_ajax_default() {
    $name = $this->req->param(2);
    $s = "Ngn.settings.$name";
    if (!empty($this->req->params[3])) {
      $s = $s.".{$this->req->params[3]}";
      $v = Config::getVarVar($name, $this->req->params[3], true);
    } else {
      $v = Config::getVar($name, true);
    }
    print "\n// Dynamic settings:\nNgn.toObj('$s');\n".$s.' = '.Arr::jsValue($v)."\n";
  }

}