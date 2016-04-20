<?php

class UserRegPhoneForm extends UserBaseForm {

  function id() {
    return 'formUserRegPhone';
  }
  
  protected function defineOptions() {
    return array_merge(parent::defineOptions(), [
      'submitTitle' => 'Продолжить'
    ]);
  }

  function __construct(array $options = []) {
    parent::__construct([
      [
        'title'    => 'Ваш телефон',
        'name'     => 'phone',
        'type'     => 'phone',
        'required' => true
      ],
//      [
//        'title'   => 'Выберите способ для подтверждения телефона',
//        'name'    => 'method',
//        'type'    => 'radio',
//        'noValue' => true,
//        'options' => [
//          'sms'   => 'по sms',
//          'phone' => 'по телефону'
//        ]
//      ],
      [
        'value' => 'Отправить код подтверждения по SMS',
        'type'  => 'button',
        'name'  => 'send'
      ],
      [
        'title'     => 'Код',
        'name'      => 'code',
        'type'      => 'num',
        'maxlength' => 4,
        'required'  => true,
        'help'      => 'Введите сюда высланный Вам код'
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
    url: '/' + Ngn.sflmFrontend + '/userRegPhone/json_send' + Ngn.String.ucfirst(method),
    onComplete: function(r) {
      btn.toggleDisabled(true);
      if (r.validError) {
        var eInput = form.eForm.getElement('[name=phone]');
        form.validator.showNewAdvice('asd', eInput, r.validError);
      }
    }
  }).get({
    phone: form.eForm.getElement('[name=phone]').get('value')
  });
};

var btn = new Ngn.Btn(eBtn, function(e) {
  if (!form.validator.validateField('phonei')) return;
  //Ngn.Frm.phoneConfirm(Ngn.Frm.getValueByName('method', form.eForm));
  Ngn.Frm.phoneConfirm('sms');
});
JS;
  }

}