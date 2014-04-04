<?php

class UserRoleForm extends Form {

  protected $userId;

  function __construct($userId) {
    $this->userId = $userId;
    parent::__construct(new Fields([
      [
        'name' => 'role',
        'title' => 'Тип профиля',
        'type' => 'userRole'
      ]
    ]));
    $user = DbModelCore::get('users', $this->userId);
    $this->setElementsData(['role' => $user['role']]);
  }
  
  protected function _update(array $data) {
    DbModelCore::update('users', $this->userId, $data);
  }

}