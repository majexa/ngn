<?php

class UsersEditForm extends UserForm {

  public $create = false;
  public $userId;

  function __construct($userId, array $options = []) {
    $this->userId = $userId;
    if (!($user = DbModelCore::get('users', $this->userId))) {
      throw new Exception("User ID={$this->userId} does not exists");
    }
    parent::__construct($options);
    if (Config::getVarVar('userReg', 'extraData')) {
      if (($userExtra = DbModelCore::get(DdCore::table('users'), $this->userId)) !== false) {
        $user['extra'] = Arr::unserialize($userExtra->r);
      }
    }
    if (Config::getVarVar('userReg', 'nameEnable')) {
      if (preg_match('/(.*) (.*)/', $user->r['name'], $m)) {
        $user->r['firstName'] = $m[1];
        $user->r['secondName'] = $m[2];
      } else {
        $user->r['firstName'] = $user->r['name'];
      }
    }
    $this->setElementsData(Arr::dropK($user->r, 'pass'));
  }

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['submitTitle' => Lang::get('save')]);
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass2');
    $n = Arr::getKeyByValue($fields, 'name', 'pass');
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass');
    $fields = Arr::injectAfter($fields, $n - 1, [
      [
        'name'  => 'passBegin',
        'title' => Lang::get('changePassword'),
        'type'  => 'headerToggle'
      ],
      [
        'name'  => 'pass',
        'title' => Lang::get('password'),
        'help'  => Lang::get('keepEmptyIfNotChanges'),
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
    Auth::save(DbModelCore::get('users', $this->userId));
  }

}
