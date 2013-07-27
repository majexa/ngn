<?php

class FieldEEmails extends FieldETextarea {

  protected function defineOptions() {
    $this->options['help'] = 'Через запятую';
  }

  protected function prepareValue() {
    if (!empty($this->options['value'])) {
      $this->options['value'] = implode(', ', array_map(function($email) {
        return trim($email);
      }, explode(',', $this->options['value'])));
    }
  }

  protected function validate2() {
    $emails = explode(',', $this->options['value']);
    foreach ($emails as $email) {
      $email = trim($email);
      if (!Misc::validEmail($email)) $this->error = "Неправильный формат e-mail'a (".htmlspecialchars($email).")";
    }
  }

}