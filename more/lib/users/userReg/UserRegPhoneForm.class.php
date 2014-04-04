<?php

class UserRegPhoneForm extends Form {

  function __construct(array $options = []) {
    $options['submitTitle'] = 'Продолжить';
    parent::__construct([
      [
        'title'    => 'Ваш телефон',
        'name'     => 'phone',
        'type'     => 'phone',
        'required' => true
      ], [
        'title'    => 'Ваш телефон',
        'name'     => 'phone',
        'type'     => 'phone',
        'required' => true
      ], [
        'title'    => 'Выберите способ для подтверждения телефона',
        'name'     => 'method',
        'type'     => 'radio',
        'noValue'  => true,
        'options'  => [
          'sms'   => 'по sms',
          'phone' => 'по телефону'
        ]
      ], [
        'value' => 'Отправить код подтверждения',
        'type'  => 'button',
        'name'  => 'send'
      ], [
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
Ngn.frm.phoneConfirm = function(method) {
  btn.toggleDisabled(false);
  new Ngn.Request.JSON({
    url: '/c/userRegPhone/json_send' + ucfirst(method),
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
  Ngn.frm.phoneConfirm(Ngn.frm.getValueByName('method', form.eForm));
});
JS;
  }

  protected function initErrors() {
    $codeEl = $this->getElement('code');
    $exists = db()->select('SELECT * FROM userPhoneConfirm WHERE phone=? AND code=?', $this->getElement('phone')->value(), $codeEl->value());
    if (!$exists) $codeEl->error('Неверный код подтверждения');
  }

}