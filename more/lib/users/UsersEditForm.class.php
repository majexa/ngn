<?php

class UsersEditForm extends UserForm {

  public $create = false;
  public $userId;

  static function splitName($name) {
    $r = [];
    if (preg_match('/(.*) (.*)/', $name, $m)) {
      $r['firstName'] = $m[1];
      $r['lastName'] = $m[2];
    } else {
      $r['firstName'] = $name;
      $r['lastName'] = '';
    }
    return $r;
  }

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
      $user->r = array_merge($user->r, self::splitName($user['name']));
    }
    $this->setElementsData(Arr::dropK($user->r, 'pass'));
  }

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['submitTitle' => Locale::get('save')]);
  }

  protected function _getFields() {
    $fields = parent::_getFields();
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass2');
    $n = Arr::getKeyByValue($fields, 'name', 'pass');
    $fields = Arr::dropBySubKeys($fields, 'name', 'pass');
    $fields = Arr::injectAfter($fields, $n - 1, [
      [
        'name'  => 'passBegin',
        'title' => Locale::get('changePassword'),
        'type'  => 'headerToggle'
      ],
      [
        'name'  => 'pass',
        'title' => Locale::get('password'),
        'help'  => Locale::get('keepEmptyIfNotChanges'),
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
