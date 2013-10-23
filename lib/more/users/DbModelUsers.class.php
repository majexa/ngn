<?php

class DbModelUsers extends DbModel {

  function __construct($table, $value, $param = 'id') {
    if ($param == 'id' and $value < 0) {
      $users = UsersCore::getSystemUsers();
      if (isset($users[$value])) {
        $this->r = [
          'id' => $value,
          'login' => $users[$value]
        ];
        return;
      }
    }
    parent::__construct($table, $value, $param);
  }

  function updateExtra(array $v) {
    $v['id'] = $this->r['id'];
    db()->create(DdCore::table(UsersCore::extraStrName), $v, true);
  }

  protected function getModelData() {
    if (!($r = parent::getModelData())) return [];
    if (Config::getVarVar('userReg', 'extraData')) {
      $r['extra'] = (new DdItems(UsersCore::extraStrName))->getItem($r['id']);
    } else {
      $r['extra'] = [];
    }
    return $r;
  }

  static function _update($id, $data) {
    if (isset($data['extra'])) {
      if (Config::getVarVar('userReg', 'extraData')) {
        $table = DdCore::table(UsersCore::extraStrName);
        $data['extra'] = Arr::filterByKeys($data['extra'], db()->fields($table));
        DbModelCore::replace($table, $id, Arr::serialize($data['extra']));
      }
      unset($data['extra']);
    }
    parent::update('users', $id, $data, true);
    if (!empty($data['pass'])) self::logout($id);
  }

  static protected function logout($userId) {
    foreach (db()->select("SELECT id, data FROM sessions WHERE data LIKE '%auth|%'") as $v) {
      $user = Session::unserialize($v['data'])['auth'];
      if ($user['id'] == $userId) db()->query("DELETE FROM sessions WHERE id=?", $v['id']);
    }
  }

  static function _create(array $data) {
    if (isset($data['extra'])) {
      $extra = $data['extra'];
      unset($data['extra']);
    }
    $data['actCode'] = Misc::randString();
    $id = parent::create('users', $data);
    if (isset($extra) and Config::getVarVar('userReg', 'extraData')) {
      DbModelCore::replace(DdCore::table(UsersCore::extraStrName), $id, Arr::serialize($extra));
    }
    return $id;
  }

  function getClean() {
    return Arr::filterByExceptKeys($this->r, ['pass', 'passClear']);
  }
  
  function checkPass($pass) {
    return Auth::cryptPass($pass) == $this->r['pass'];
  }

  static function beforeCreateUpdate(array &$data) {
    if (!empty($data['pass'])) {
      $data['passClear'] = $data['pass'];
      $data['pass'] = Auth::cryptPass($data['pass']);
    }
  }
  
  static function unpack(array &$r) {
    if (!empty($r['phone'])) $r['phone'] = '+'.$r['phone'];
  }
  
  static function searchUser($mask) {
    $mask = $mask.'%';
    return db()->selectCol("
      SELECT id AS ARRAY_KEY, login FROM users WHERE
      active=1 AND id>0 AND login LIKE ? LIMIT 10", 
      $mask);
  }

}