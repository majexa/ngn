<?php

abstract class AgiBeepAcceptAction extends AgiAction {

  function action() {
    LogWriter::v('posled', 'playback intro');
    $this->agi->playback($this->options['introSound']);
    //LogWriter::v('posled', 'usleep');
    //usleep(0.3 * 1000000);
    LogWriter::v('posled', 'set dummy true');
    $this->agi->setVar('dummy', true);
    LogWriter::v('posled', 'set actionComplete true');
    $this->agi->setVar('actionComplete', true);
    LogWriter::v('posled', 'accept');
    $this->accept();
    LogWriter::v('posled', 'ok sound');
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