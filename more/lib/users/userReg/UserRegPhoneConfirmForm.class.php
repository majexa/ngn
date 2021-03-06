<?php

abstract class UserRegPhoneConfirmForm extends Form {

  public $registeredUser = null;

  function __construct(array $fields = [], array $options = []) {
//    if (($userId = Auth::get('id'))) {
//      $this->authorizedUser = DbModelCore::get('users', $userId);
//    }
//    if ($this->authorizedUser) {
//      $fields[] = [
//        'type' => 'staticText',
//        'text' => 'Телефон для связи:<br>'.$this->authorizedUser['phone'],
//      ];
//    }
//    else {
    $fields = array_merge($fields, [
      [
        'type' => 'groupBlock',
        'name' => 'ph'
      ],
      [
        'title'    => 'Телефон для связи',
        'name'     => 'phone',
        'required' => true,
        'type'     => 'phone'
      ],
      [
        'type' => 'staticText',
        'text' => '<p class="label">&nbsp;</p> <a class="btn sendPass icon ok"><span><i></i>Отправить пароль</span></a>',
      ],
      [
        'type' => 'groupBlock'
      ],
      [
        'title'     => 'Код',
        'name'      => 'code',
        'required'  => true,
        'type'      => 'text',
        'maxlength' => 4
      ],
    ]);
//    }
    parent::__construct($fields, $options);
  }

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Зарегистрироваться'
    ]);
  }

  protected function jsInlineConfirmedPhone() {
    if ($this->registeredUser) return '';
    return <<<JS

var form = Ngn.Form.forms.{$this->id()};
new Ngn.Btn(form.eForm.getElement('.sendPass'), function() {
  var ePhoneField = form.eForm.getElement('.name_phone input');
  if (!ePhoneField.get('value')) {
    alert('Заполните телефон');
    return;
  }
  if (!form.validator.validateField(ePhoneField)) {
    return;
  }
  this.toggleDisabled(false);
  new Ngn.Request.JSON({
    url: '/default/userRegPhone/json_sendSms',
    onComplete: function(r) {
      this.toggleDisabled(true);
      if (r.validError) {
        var eInput = form.eForm.getElement('[name=phone]');
        form.validator.showNewAdvice('e' + Ngn.String.hashCode(r.validError), eInput, r.validError);
      }
    }.bind(this)
  }).post({
      phone: ePhoneField.get('value')
    });
});
JS;
  }

  function html() {
    $html = parent::html();
    $html .= '
<style>
.hgrp_ph .element {
float: left;
}
.name_code input {
width: 50px;
}
</style>
';
    return $html;
  }

  protected function userRole() {
    return '';
  }

  protected function _update(array $data) {
    if (!$this->registeredUser) {
      if (!($user = DbModelCore::get('users', $data['phone'], 'phone'))) {
        $id = DbModelCore::create('users', [
          'role'   => $this->userRole(),
          'phone'  => $data['phone'],
          'pass'   => $data['code'],
          'active' => 1
        ]);
        $user = DbModelCore::get('users', $id);
        $this->registeredUser = $user;
      }
    }
    DdCore::imDefault('profile')->create($data);
  }

  protected function _initErrors() {
//    $phone = $this->getElement('phone');
//    if (!$phone->valueChanged) {
//      $phone->error('Телефон не изменился');
//      return;
//    }
    $this->initCodeError();
  }

  protected function initCodeError() {
    if (!Config::getVarVar('userReg', 'phoneConfirm')) return;
    $codeEl = $this->getElement('code');
    if (getConstant('TESTING') and $codeEl->value() == '123') return;
    $exists = db()->selectCell('SELECT id FROM userPhoneConfirm WHERE phone=? AND code=?', $this->getElement('phone')->value(), $codeEl->value());
    if (!$exists) $codeEl->error('Неверный код подтверждения');
  }

}
