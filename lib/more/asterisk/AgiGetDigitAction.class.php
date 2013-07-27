<?php

abstract class AgiGetDigitAction extends AgiAction {

  protected function defineOptions() {
    parent::defineOptions();
    $this->options['repeatSound'] = "ivr/{$this->name}/repeat";
  }

  protected $attempts, $attemptsLimit = 3;

  /**
   * Возвращает введёную пользователем цифру если она присутствует разрешенных ($this->getAllowedDigits),
   * или false в обратном случае, либо если пользователь нажал '#'
   *
   * @return bool|int
   */
  protected function getDigit() {
    $this->attempts = 0;
    $this->r = $this->agi->getDigit($this->options['introSound']);
    while (1) {
      if ($this->attempts == $this->attemptsLimit) return false;
      if ($this->attempts > 0) $this->r = $this->agi->getDigit($this->options['repeatSound']);
      $this->conlog('Result: '.$this->r['result']);
      if (($digit = $this->_getDigit()) !== false) {
        $this->agi->conlog("Digit '$digit' is correct!");
        return $digit;
      }
      elseif (!empty($this->options['wrongDigitSound'])) {
        $this->agi->conlog('Play wrongDigitSound');
        $this->agi->playback($this->options['wrongDigitSound']);
      }
      $this->agi->conlog('next attempt');
      $this->attempts++;
    }
  }

  /**
   * @var Agi result
   */
  protected $r;

  protected function _getDigit() {
    $digit = $this->r['result'];
    $this->agi->conlog("User input: $digit");
    $allowedDigits = $this->getAllowedDigits();
    $this->agi->conlog("Allowed digits: ".implode(', ', $allowedDigits));
    if ($digit === '' or !in_array($digit, $this->getAllowedDigits())) return false;
    return $digit;
  }

  function action() {
    $digit = $this->getDigit();
    // действие в digitAction или otherDigitAction должно выполнится мгновенно, поэтому выставляем флаг actionComplete уже здесь
    $this->agi->setVar('actionComplete', true);
    if ($digit !== false) {
      $this->agi->conlog("digitAction");
      $this->digitAction($digit);
    } else {
      $this->agi->conlog("wrongDigitAction");
      $this->wrongDigitAction();
    }
  }

  abstract protected function getAllowedDigits();

  abstract protected function digitAction($digit);

  abstract protected function wrongDigitAction();

}