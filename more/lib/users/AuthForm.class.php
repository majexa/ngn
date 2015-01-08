<?php

class AuthForm extends Form {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'name'        => 'auth',
      'submitTitle' => 'Войти'
    ]);
  }

  function __construct(array $options = []) {
    $loginField = UserRegCore::getLoginField();
    $loginField['name'] = 'authLogin';
    $fields = [
      $loginField,
      [
        'name'     => 'authPass',
        'title'    => 'Пароль',
        'type'     => 'password',
        'required' => true
      ]
    ];
    parent::__construct(new Fields($fields), $options);
  }

  function id() {
    return 'formAuth';
  }

  function initErrors() {
    parent::initErrors();
    $data = $this->getData();
    if (!Auth::loginByRequest($data['authLogin'], $data['authPass'])) {
      if (in_array(Auth::$errors[0]['code'], [
        Auth::ERROR_AUTH_NO_LOGIN,
        Auth::ERROR_AUTH_USER_NOT_ACTIVE,
        Auth::ERROR_EMPTY_LOGIN_OR_PASS
      ])) $this->getElement('authLogin')->error(Auth::$errors[0]['text']);
      else $this->getElement('authPass')->error(Auth::$errors[0]['text']);
    }
  }

}