<?php

class FieldEUser extends FieldEAutocompleter {

  protected function defineOptions() {
    $this->options['help'] = 'Для поиска начните вводить email пользователя';
  }

  protected function validate2() {
    if (!DbModelCore::get('users', $this->options['value'])) {
      $this->error = "Пользователя с ID={$this->options['value']} не существует";
    }
  }
  
  function _html() {
    if (empty($this->options['value'])) $title = null;
    elseif (($user = DbModelCore::get('users', $this->options['value'])) !== false) {
      $title = UsersCore::getTitle($user);
    }
    else $title = null;
    return $this->__html($title);
  }

}
