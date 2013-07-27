<?php

class CtrlCommonDdImageResizer extends CtrlCommon {

  function action_json_asd() {
    $r =  new Form(new Fields([[
      'title' => 'asdqwdqwdqw'
    ]]));
    if ($r->isSubmitted()) {
      $this->json['dialog'] = [
        'cls' => 'Ngn.Dialog.Loader.Simple',
        'options' => [
          'title' => 'suck dick'
        ]
      ];
      return;
    }
    return $r;
  }
  
  function action_json_default() {
  }

}
