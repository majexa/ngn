<?php

class CtrlCommonUploadTemp extends CtrlCommon {

  function action_json_default() {
    LogWriter::v('files', $this->req->files);
    if (empty($_FILES)) return;
    O::get('UploadTemp', [
      'formId' => $this->req->reqNotEmpty('formId'),
      'tempId' => $this->req->reqNotEmpty('tempId'),
      'multiple' => !empty($this->req->r['multiple'])
    ])->upload($this->req->files, $this->req->reqNotEmpty('fn'));
  }
  
  function action_deleteOld() {
    $this->hasOutput = false;
    UploadTemp::deleteOld();
  }
  
}
