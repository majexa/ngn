<?php

class FieldEEmail extends FieldEText {

  protected function defineOptions() {
    return array_merge(parent::defineOptions(), ['cssClass' => 'validate-email']);
  }

  protected function validate2() {
    if (!Misc::validEmail($this->options['value'])) $this->error = "Неправильный формат e-mail'a";
  }

}
