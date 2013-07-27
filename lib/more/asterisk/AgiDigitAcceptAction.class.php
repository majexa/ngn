<?php

abstract class AgiDigitAcceptAction extends AgiGetDigitAction {

  protected function defineOptions() {
    parent::defineOptions();
    $this->options['declineSound'] = "ivr/{$this->name}/decline";
  }

  protected function getAllowedDigits() {
    return [1, 0];
  }

  function digitAction($digit) {
    if ($digit == 1) {
      $this->accept();
      if (!empty($this->options['okSound'])) $this->agi->playback($this->options['okSound']);
    } else {
      $this->agi->conlog('decline');
      $this->decline();
      if (!empty($this->options['declineSound'])) {
        $this->agi->conlog('play decline sound');
        $this->agi->playback($this->options['declineSound']);
      }
    }
  }

  function otherDigitAction() {
    $this->decline();
  }

  protected function wrongDigitAction() {
    $this->decline();
  }

  function hangup() {
    if (!$this->agi->getVar('actionComplete')) $this->decline();
  }

  abstract protected function accept();

  abstract protected function decline();

}