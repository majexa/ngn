<?php

abstract class AgiBeepAcceptAction extends AgiAction {

  function action() {
    $this->agi->playback($this->options['introSound']);
    $this->agi->setVar('actionComplete', true);
    $this->accept();
    $this->agi->playback($this->options['okSound']);
  }

  function hangup() {
    if (!$this->agi->getVar('actionComplete')) $this->decline();
  }

  abstract protected function accept();

  abstract protected function decline();

}