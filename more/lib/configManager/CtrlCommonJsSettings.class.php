<?php

class CtrlCommonJsSettings extends CtrlBase {

  protected function sflmStore() {
  }

  function action_ajax_default() {
    header('Content-type: application/javascript; charset='.CHARSET);
    $name = $this->req->param(2);
    $s = "Ngn.settings.$name";
    if (!empty($this->req->params[3])) {
      $s = $s.".{$this->req->params[3]}";
      $v = Config::getVarVar($name, $this->req->params[3], true);
    } else {
      $v = Config::getVar($name, true);
    }
    print "\n// Dynamic settings:\nNgn.Object.fromString('$s');\n".$s.' = '.json_encode($v)."\n";
  }

}