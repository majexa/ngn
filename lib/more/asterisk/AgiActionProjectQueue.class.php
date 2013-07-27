<?php

trait AgiActionProjectQueue {

  protected function pqAdd($actionName, array $_data = []) {
    $data = ['id' => $this->agi->getVar('id')];
    if ($_data) $data = array_merge($data, $_data);
    (new ProjectQueue($this->name))->addDefault($actionName, $data);
  }

}