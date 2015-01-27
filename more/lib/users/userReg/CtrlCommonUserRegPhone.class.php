<?php

class CtrlCommonUserRegPhone extends CtrlCammon {

  function action_json_form() {
    $form = new UserRegPhoneForm(['req' => $this->req, 'defaultsFromReq' => true]);
    $form->action = '/'.Sflm::frontendName(true).'/userRegPhone/json_form';
    $this->json['title'] = 'Регистрация';
    if ($form->update()) {
      $this->json['nextFormUrl'] = '/'.Sflm::frontendName(true).'/userReg/json_form?'.http_build_query($form->getData());
      return null;
    }
    return $this->jsonFormAction($form);
  }

  static function expireTime() {
    return 60 * 60;
  }

  protected function sendCode($method) {
    if (!Misc::validPhone($this->req->r['phone'])) {
      $this->json['validError'] = 'Неправильный формат телефона';
      return;
    }
    $phone = trim($this->req->r['phone'], '+ ');
    if (db()->select('SELECT * FROM users WHERE phone=?', $phone)) {
      $this->json['validError'] = 'Пользователь с таким телефоном уже существует';
      return;
    }
    $r = db()->selectRow('SELECT * FROM userPhoneConfirm WHERE dateCreate > ? AND phone=?', Date::db(time() - self::expireTime()), $phone);
    $maxAttempts = 15;
    if ($r) {
      if ($r['attempts'] >= $maxAttempts) {
        $this->json['validError'] = 'Вы исчерпали лимит попыток. Попробуйте ещё раз через час';
        return;
      }
      $code = $r['code'];
      $attempts = $r['attempts'] + 1;
    }
    else {
      $code = Misc::randNum(4);
      $attempts = 1;
    }
    $d = [
      'phone'      => $phone,
      'code'       => $code,
      'attempts'   => $attempts,
      'dateCreate' => Date::db()
    ];
    if ($r) $d['id'] = $r['id'];
    $id = db()->create('userPhoneConfirm', $d, true);
    if ($method == 'phone') {
      (new Asterisk)->addOutgoingCall($phone, $id, [
        'actionName' => 'userRegPhone',
        'code'       => $code
      ]);
    }
    else {
      (new Smsc)->send_sms($phone, "CODE: $code");
    }
  }

  function action_json_sendPhone() {
    $this->sendCode('phone');
  }

  function action_json_sendSms() {
    $this->sendCode('sms');
  }

}