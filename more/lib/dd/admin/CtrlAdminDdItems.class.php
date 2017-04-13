<?php


class AsdForm extends Form {

  function __construct() {
    parent::__construct([[
      'title' => 'asd',
      'name' => 'image',
      'type' => 'image',
      'multiple' => true
    ]]);
  }

}

class CtrlAdminDdItems extends CtrlAdmin {
  use DdCrudCtrl;

  protected $structure;

  protected function init() {
    $this->d['structure'] = $this->structure = (new DdStructureItems)->getItemByField('name', $this->getStrName());
    Misc::checkEmpty($this->structure, 'structure '.$this->getStrName().' does not exists');

    $this->setModuleTitle($this->structure['title']);
    Sflm::frontend('js')->addClass('Ngn.DdGrid.Admin');
  }

  protected function _getIm() {
    return new DdItemsManager($this->items(), $this->objectProcess(new DdForm(new DdFields($this->getStrName(), [
      //'forceShow'     => ['userId'],
      'getDisallowed' => true
     ]), $this->getStrName()), 'form'));
  }

  protected function getParamActionN() {
    return 3;
  }

  protected function getStrName() {
    return $this->req->param(2);
  }

  protected function id() {
    return $this->req['id'] ?: $this->req->param(4);
  }

  function action_edit() {
    if ($this->getIm()->requestUpdate($this->id())) {
      $this->d['tpl'] = 'common/success';
      return;
    }
    $this->d['form'] = $this->getIm()->form->html();
    $this->d['tpl'] = 'common/form';
  }

  function action_default() {
    $this->d['settings'] = $this->structure['settings'];
    $fields = DdCore::imDefault($this->getStrName())->form->fields->getFieldsF();
    if (count($fields) == 1 and isset($fields['image'])) {
      $this->d['enableImageMultiUpload'] = true;
    }
    $this->req->param(2); // required
  }

  function action_form() {
    $this->d['tpl'] = 'common/form';
    $this->d['form'] = DdCore::imDefault($this->getStrName())->form->setElementsData()->html();
  }

  function action_json_settings() {
    $this->json['title'] = 'Настройки записей';
    return $this->jsonFormActionUpdate(new AdminDdItemsSettingsForm($this->structure['id']));
  }

  function action_json_imageMultiUpload() {
    return $this->jsonFormActionUpdate(new MultiImageUploadForm([
      'baseUrl' => '/admin/ddItems/' . $this->req->param(2)
    ]));
  }

  function action_json_upload() {
    $im = DdCore::imDefault($this->req->param(2));
    foreach ($this->req->files['images'] as $file) {
      $im->create(['image' => $file]);
    }
  }

  function processForm(DdForm $form) {
    $form->options['deleteFileUrl'] = $this->tt->getPath(2).'/'.$this->req->param(2).'/deleteFile?id='.$this->req['id'];
  }

  function oProcessItemsAdmin(DdDbItemsExtended $items) {
    $items->getNonActive = true;
  }

}