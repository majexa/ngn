<?php

class UsersEditForm extends UsersForm {

  public $create = false;
  public $userId;

  function __construct($userId, array $options = []) {
    $this->userId = $userId;
    if (!($data = DbModelCore::get('users', $this->userId))) {
      throw new Exception("User ID={$this->userId} does not exists");
    }
    parent::__construct($options);
    if (Config::getVarVar('userReg', 'extraData')) {
      if (($r = DbModelCore::get(DdCore::table('users'), $this->userId)) !== false) {
        $data['extra'] = Arr::unserialize($r->r);
      }
    }
    $this->setElementsData(Arr::dropK($data->r, 'pass'));
  }

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['submitTitle' => 'Сохранить']);
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass2');
    $n = Arr::getKeyByValue($fields, 'name', 'pass');
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass');
    $fields = Arr::injectAfter($fields, $n - 1, [
      [
        'name'  => 'passBegin',
        'title' => 'Изменить пароль',
        'type'  => 'headerToggle'
      ],
      [
        'name'  => 'pass',
        'title' => 'Пароль',
        'help'  => 'Оставьте пустым, если не хотите менять',
        'type'  => 'password'
      ],
      [
        'type' => 'headerClose'
      ]
    ]);
    return $fields;
  }

  protected function _update(array $data) {
    if (empty($data['pass'])) unset($data['pass']);
    DbModelCore::update('users', $this->userId, $data, true);
  }

}
