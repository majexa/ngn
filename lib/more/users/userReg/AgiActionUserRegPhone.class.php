<?php

class AgiActionUserRegPhone extends AgiAction {

  function action() {
    $this->agi->exec_setlanguage('ru');
    $this->agi->say_digits($this->agi->getVar('code'));
    sleep(2);
    $this->agi->say_digits($this->agi->getVar('code'));
    sleep(2);
    $this->agi->say_digits($this->agi->getVar('code'));
    sleep(1);
  }

}