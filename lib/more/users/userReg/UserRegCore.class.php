<?php

class UserRegCore {

  static function getLoginTitle() {
    if (Config::getVarVar('userReg', 'loginAsFullName')) return 'Ф.И.О.';
    else
      return 'Логин';
  }

  static function getAuthLoginTitle() {
    if (Config::getVarVar('userReg', 'loginEnable')) $loginTitle[] = Config::getVarVar('userReg', 'loginAsFullName') ? 'Ф.И.О.' : 'Логин';
    if (Config::getVarVar('userReg', 'emailEnable')) $loginTitle[] = 'E-mail';
    if (Config::getVarVar('userReg', 'phoneEnable')) $loginTitle[] = 'Телефон';
    return implode(' / ', $loginTitle);
  }

  static function getLoginField() {
    if (Config::getVarVar('userReg', 'loginAsFullName')) {
      return [
        'name'      => 'login',
        'title'     => 'Ф.И.О.',
        'validator' => 'fullName',
        'required'  => true
      ];
    }
    else {
      return [
        'name'     => 'login',
        'title'    => 'Логин',
        'required' => true
      ];
    }
  }

}
