<?php

class UsersCore {

  static function sendLostPass($email) {
    if (($user = DbModelCore::get('users', $email, 'email')) === false) return false;
    return (new SendEmail)->send($user['email'], 'Восстановление пароля', 'Ваш пароль: '.$user['passClear']);
  }

  static function extendImageData(array &$users) {
    foreach ($users as &$v) $v += UsersCore::getImageData($v['id']);
  }

  static function generateNames() {
    set_time_limit_q(0);
    foreach (db()->query("SELECT id, login FROM users WHERE name='' AND id>0") as $v) {
      try {
        $new = Misc::domain($v['login']);
      } catch (Exception $e) {

      }
      db()->query('UPDATE users SET name=? WHERE id=?d', $new, $v['id']);
    }
    foreach (db()->query("SELECT id, login FROM users WHERE id>0 AND name='' ORDER BY id") as $v) {
      try {
        $_name = $name = Misc::domain($v['login']);
      } catch (Exception $e) {
        try {
          $_name = $name = Misc::domain('a'.$v['login']);
        } catch (Exception $e) {
          print '<p>Error while renerating names: '.$e->getMessage().'</p>';
          continue;
        }
      }
      $n = 1;
      while (db()->query('SELECT id FROM users WHERE name=?', $name)) {
        $name = $_name.$n;
        $n++;
        if ($n == 100) throw new Exception('Limit of attempts to generate name is 100. login="'.$v['login'].'". last name="'.$name.'"');
      }
      db()->query('UPDATE users SET name=? WHERE id=?d', $name, $v['id']);
    }
  }

  // ------- html -----------

  static function avatarImg($userId, $login) {
    $v = UsersCore::getImageData($userId);
    return self::_avatarImg($userId, $login, empty($v['sm_image']) ? null : $v['sm_image']);
  }

  static $avatarCachePath = 'user-cache';

  static function avatarImgResized($userId, $login, $w, $h) {
    if (!in_array([$w, $h], Config::getVar('avatarSizes'))) throw new Exception("Size {$w}x{$h} not allowed");
    $path = self::imagePath($userId);
    $file = UPLOAD_PATH.'/'.$path;
    if (!file_exists($file)) return self::_avatarImg($userId, $login);
    $resizedPath = self::$avatarCachePath.'/'.$w.'x'.$h.'/'.$userId.'.jpg';
    if (!file_exists(UPLOAD_PATH.'/'.$resizedPath)) {
      Dir::make(UPLOAD_PATH.'/'.dirname($resizedPath));
      O::get('Image')->resizeAndSave(UPLOAD_PATH.'/'.$path, UPLOAD_PATH.'/'.$resizedPath, $w, $h);
    }
    return self::_avatarImg($userId, $login, UPLOAD_DIR.'/'.$resizedPath);
  }

  static function cleanAvatarCache($userId) {
    foreach (Config::getVar('avatarSizes') as $v) File::delete(UPLOAD_PATH.'/'.self::$avatarCachePath.'/'.$v[0].'x'.$v[1].'/'.$userId.'.jpg');
  }

  static function _avatarImg($userId, $login, $path = null) {
    return '<img src="/'.(!empty($path) ? $path : (file_exists(WEBROOT_PATH.'/m/img/no-avatar.gif') ? 'm/img/no-avatar.gif' : 'i/img/no-avatar.gif')).'" title="'.$login.'" />';
  }

  static function avatar($userId, $login, $class = '') {
    $v = UsersCore::getImageData($userId);
    return '<div class="avatar hover'.(!empty($v['sm_image']) ? '' : ' noAvatar').($class ? ' '.$class : '').'">'.'<a href="'.Tt()->getUserPath($userId).'">'.self::_avatarImg($userId, $login, empty($v['sm_image']) ? null : $v['sm_image']).'</a></div>';
  }

  static function avatarAndLogin($userId, $login) {
    return self::avatar($userId, $login).'<h2>'.Tt()->getUserTag($userId, $login).'</h2>';
  }

  static function getImageData($userId) {
    $path = 'user/'.$userId;
    if (file_exists(UPLOAD_DIR.'/'.$path.'.jpg')) $path = $path.'.jpg';
    elseif (file_exists(UPLOAD_DIR.'/'.$path.'.png')) $path = $path.'.png';
    elseif (file_exists(UPLOAD_DIR.'/'.$path.'.gif')) $path = $path.'.gif';
    elseif (file_exists(UPLOAD_DIR.'/'.$path.'.bmp')) $path = $path.'.bmp';
    else $path = false;

    if ($path) {
      $path = UPLOAD_DIR.'/'.$path;
      return [
        'image'    => $path,
        'sm_image' => Misc::getFilePrefexedPath($path, 'sm_'),
        'md_image' => Misc::getFilePrefexedPath($path, 'md_')
      ];
    }
    return [];
  }

  static function getRoles() {
    $roles = [
      [
        'name'  => '',
        'title' => 'Пользователь',
        'text'  => ''
      ]
    ];
    if (($_roles = Config::getVarVar('role', 'roles', true)) !== false) $roles = Arr::append($roles, $_roles);
    return $roles;
  }

  const profileStrName = 'profile';

  static function profile($userId) {
    return DbModelCore::get(DdCore::table(self::profileStrName), $userId, 'userId');
  }

  static function name(DbModelUsers $user) {
    $r = Config::getVarVar('profile', 'userInfoBlockType') == 'profileField' ? self::profile($user['id'])->r[Config::getVarVar('profile', 'userInfoBlockField')] : $user['login'];
    if (empty($r)) throw new Exception('User ID='.$user['id'].' has empty name');
    return $r;
  }

  static function getSystemUsers() {
    return [
      self::allUsersId        => 'Все пользователи',
      self::registeredUserId => 'Зарегистированые пользователи'
    ];
  }

  const registeredUserId = -1;
  const allUsersId = -2;

  const extraStrName = 'users';

  static function titleName() {
    return Config::getVarVar('userReg', 'titleName', true) ?: 'login';
  }

  static function getTitle($user) {
    if (!is_object($user)) {
      $userId = $user;
      $user = DbModelCore::get('users', $user);
      if (empty($user)) return 'no user '.$userId;
    }
    return BracketName::getValue($user->r, self::titleName());
  }

  static $staticTitleNames = ['login', 'email', 'phone'];

  static function getTitleNames() {
    $names = [];
    foreach (self::$staticTitleNames as $v) {
      if (Config::getVarVar('userReg', $v.'Enable')) $names[$v] = $v;
    }
    if (Config::getVarVar('userReg', 'extraData')) {
      foreach ((new DdFields(UsersCore::extraStrName))->getRequired() as $v) {
        $names["extra[{$v['name']}]"] = $v['title'];
      }
    }
    return $names;
  }

  static function getDefaultExtraField() {
    return preg_replace('/extra\[(.*)\]/', '$1', self::titleName());
  }

  static function getDefaultStaticField() {
    return Config::getVarVar('userReg', 'titleName');
  }

  static function getDefaultTable() {
    return self::isExtra() ? DdCore::table(UsersCore::extraStrName) : 'users';
  }

  static function getDefaultField() {
    return self::isExtra() ? self::getDefaultExtraField() : self::getDefaultStaticField();
  }

  static protected function isExtra() {
    return strstr(Config::getVarVar('userReg', 'titleName'), 'extra[');
  }

  static function getUserOptions() {
    $field = self::getDefaultField();
    $table = self::getDefaultTable();
    return db()->selectCol("SELECT id AS ARRAY_KEY, $field FROM $table ORDER BY $field LIMIT 200");
  }

}