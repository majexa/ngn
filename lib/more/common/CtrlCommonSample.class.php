<?php

function asd() {
  throw new Exception('123');
}

class CtrlCommonSample extends CtrlCommon {

	function action_default() {
	  print 123;
	}
  function action_json_asd() {
    asd();
    $this->json = [1];
  }

}
