<?php

class UserRegPhoneForm extends Form {

  function id() {
    return 'formReg';
  }

  function __construct(array $options = []) {
    $options['submitTitle'] = 'Продолжить';
    parent::__construct([
      [
        'title'    => 'Ваш телефон',
        'name'     => 'phone',
        'type'     => 'phone',
        'required' => true
      ],
      [
        'title'    => 'Ваш телефон',
        'name'     => 'phone',
        'type'     => 'phone',
        'required' => true
      ],
      [
        'title'   => 'Выберите способ для подтверждения телефона',
        'name'    => 'method',
        'type'    => 'radio',
        'noValue' => true,
        'options' => [
          'sms'   => 'по sms',
          'phone' => 'по телефону'
        ]
      ],
      [
        'value' => 'Отправить код подтверждения',
        'type'  => 'button',
        'name'  => 'send'
      ],
      [
        'title'    => 'Код',
        'name'     => 'code',
        'type'     => 'num',
        'required' => true,
        'help'     => 'Введите сюда высланный Вам код'
      ]
    ], $options);
  }

  function jsSendConfirm() {
    return <<<JS
var form = Ngn.Form.forms['{$this->id()}'];
var eBtn = form.eForm.getElement('.name_send .btn');
Ngn.Frm.phoneConfirm = function(method) {
  btn.toggleDisabled(false);
  new Ngn.Request.JSON({
    url: '/' + Ngn.sflmFrontend + '/userRegPhone/json_send' + ucfirst(method),
    onComplete: function(r) {
      btn.toggleDisabled(true);
      if (r.validError) {
        eInput = form.eForm.getElement('[name=phone]');
        form.validator.showNewAdvice('asd', eInput, r.validError);
      }
    }
  }).get({
    phone: form.eForm.getElement('[name=phone]').get('value')
  });
};

var btn = new Ngn.Btn(eBtn, function(e) {
  if (!form.validator.validateField('phonei')) return;
  Ngn.Frm.phoneConfirm(Ngn.Frm.getValueByName('method', form.eForm));
});
JS;
  }

  protected function initErrors() {
    parent::initErrors();
    $this->initCodeError();
  }

  protected function initCodeError() {
    if (!Config::getVarVar('userReg', 'phoneConfirm')) return;
    $codeEl = $this->getElement('code');
    if (IS_DEBUG and $codeEl->value() == '123') return;
    $exists = db()->selectCell('SELECT id FROM userPhoneConfirm WHERE phone=? AND code=?', $this->getElement('phone')->value(), $codeEl->value());
    if (!$exists) $this->globalError('Неверный код подтверждения');
  }

}