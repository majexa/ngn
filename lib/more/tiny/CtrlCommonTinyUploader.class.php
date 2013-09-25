<?php

abstract class CtrlCommonTinyUploader extends CtrlCommonTinyDialog {
  
  protected $tinyAttachId;
  protected $fileFieldName = 'file';
  protected $title = 'Вставка файла';
  
  protected function init() {
    parent::init();
    $this->tinyAttachId = $this->req->reqNotEmpty('tinyAttachId');
  }
  
  abstract protected function getFields();
  abstract protected function setJson(Form $oF);
  
  function action_json_default() {
    $oF = new Form(new Fields($this->getFields()), [
      'submitTitle' => 'Вставить'
    ]);
    $ut = UploadTemp::extendFormOptions($oF);
    if ($oF->isSubmittedAndValid()) {
      $this->setJson($oF);
      $ut->delete();
      return;
    }
    $this->json['title'] = $this->title;
    if ($oF->isSubmitted()) die2($oF->lastError));
    return $oF;
  }

}