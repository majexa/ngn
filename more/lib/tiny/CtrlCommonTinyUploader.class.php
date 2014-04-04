<?php

abstract class CtrlCommonTinyUploader extends CtrlCommonTinyDialog {

  protected $tinyAttachId;
  protected $fileFieldName = 'file';
  protected $title = 'Вставка файла';

  protected function init() {
    parent::init();
    $this->tinyAttachId = $this->req->reqNotEmpty('tinyAttachId');
  }

  /**
   * @return array
   */
  abstract protected function getFields();

  abstract protected function setJson(Form $oF);

  function action_json_default() {
    $form = new Form(new Fields($this->getFields()), [
      'submitTitle' => 'Вставить'
    ]);
    $ut = UploadTemp::extendFormOptions($form);
    if ($form->isSubmittedAndValid()) {
      $this->setJson($form);
      $ut->delete();
      return;
    }
    $this->json['title'] = $this->title;
    return $form;
  }

}