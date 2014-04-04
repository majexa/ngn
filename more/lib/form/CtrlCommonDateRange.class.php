<?php

class CtrlCommonDateRange extends CtrlCammon {

  function action_ajax_default() {
    $from = (new FieldEDate([
      'name' => 'from'
    ]))->html();
    $to = (new FieldEDate([
      'name' => 'to'
    ]))->html();
    $this->ajaxOutput = "<div>$from</div><div>$to</div>";
  }

}