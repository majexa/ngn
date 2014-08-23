<?php

class CtrlCommonUploadTemp extends CtrlCommon {

  protected function sflmStore() {}

  function action_json_default() {
    if (empty($this->req->files)) return;
    (new UploadTemp([
      'formId' => $this->req->reqNotEmpty('formId'),
      'tempId' => $this->req->reqNotEmpty('tempId'),
      'multiple' => !empty($this->req->r['multiple'])
    ]))->upload($this->req->files, $this->req->reqNotEmpty('fn'));
  }
  
  function action_deleteOld() {
    $this->hasOutput = false;
    UploadTemp::deleteOld();
  }
  
}
