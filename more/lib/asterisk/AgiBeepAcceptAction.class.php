<?php

abstract class AgiBeepAcceptAction extends AgiAction {

  function action() {
    LogWriter::v('posled', 'playback intro');
    $this->agi->conlog('playback intro');
    $this->agi->playback($this->options['introSound']);
    $this->agi->conlog('set dummy true');
    $this->agi->setVar('dummy', true);
    $this->agi->conlog('set actionComplete true');
    $this->agi->setVar('actionComplete', true);
    $this->agi->conlog('accept');
    $this->accept();
    $this->agi->conlog('ok sound');
    $this->agi->playback($this->options['okSound']);
  }

  function hangup() {
    LogWriter::v('posled', 'hangup. actionComplete='.$this->agi->getVar('actionComplete'));
    LogWriter::v('hangup', $this->agi->getVar('actionComplete'));
    if (!$this->agi->getVar('actionComplete')) $this->decline();
  }

  abstract protected function accept();

  abstract protected function decline();

}