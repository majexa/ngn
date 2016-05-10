<?php

class UserForm extends UserBaseForm {

  protected $filterFields = ['login', 'user', 'pass', 'email', 'name', 'phone', 'extra', 'role'];
  public $strName = UsersCore::extraStrName;

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'active' => !(bool)Config::getVarVar('userReg', 'activation')
    ]);
  }

  public function __construct(array $options = []) {
    parent::__construct(new Fields($this->_getFields()), $options);
  }

  protected function _getFields() {
    $fields = [];
    if (Config::getVarVar('userReg', 'loginEnable')) $fields[] = UserRegCore::getLoginField();
    if (Config::getVarVar('userReg', 'emailEnable')) {
      $fields[] = [
        'name'         => 'email',
        'title'        => 'E-mail',
        'type'         => 'email',
        'required'     => true,
        'autocomplete' => 'off'
      ];
    }
    if (Config::getVarVar('userReg', 'nameEnable')) {
      $fields[] = [
        'name'         => 'firstName',
        'title'        => 'First Name',
        'required'     => true,
      ];
      $fields[] = [
        'name'         => 'lastName',
        'title'        => 'Last Name',
        'required'     => true,
      ];
    }
//    if (Config::getVarVar('userReg', 'phoneEnable')) {
//      $fields[] = [
//        'name'     => 'phone',
//        'title'    => 'Телефон',
//        'type'     => 'phone',
//        'required' => true
//      ];
//    }
    $fields[] = [
      'name'         => 'pass',
      'title'        => Lang::get('password'),
      'type'         => 'password',
      'required'     => true
    ];
    if (Config::getVarVar('userReg', 'roleEnable', true)) {
      $fields[] = $this->getRoleField();
    }
    if (Config::getVarVar('userReg', 'extraData')) {
      $fields = array_merge($fields, [
        [
          'type'  => 'headerToggle',
          'title' => 'Дополнительно'
        ]
      ], array_map(function ($v) {
        $v['name'] = 'extra['.$v['name'].']';
        return $v;
      }, (new DdFields(UsersCore::extraStrName, $this->extraFieldsOptions()))->getFieldsF()));
    }
    return $fields;
  }

  protected function extraFieldsOptions() {
    return [];
  }

  protected function init() {
    parent::init();
    if (!empty($this->fields->fields['phone'])) $this->fields->fields['phone']['options']['disabled'] = true;
  }

  protected function getRoleField() {
    return [
      'title' => 'Тип профиля',
      'name'  => 'role',
      'type'  => 'userRole',
      'required' => true
    ];
  }

}