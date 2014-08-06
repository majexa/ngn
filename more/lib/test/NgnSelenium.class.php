<?php

class NgnSeleniumStrict extends Testing_Selenium {

  protected function doCommand($verb, $args = []) {
    $r = parent::doCommand($verb, $args);
    if (preg_match('/ERROR: (.*)/', $r, $m)) {
      throw new NgnException($m[1]);
    }
    return $r;
  }

}

class NgnSelenium {

  const DEFAULT_TIMEOUT = 10000;

  /**
   * @var Testing_Selenium
   */
  public $s;

  public function __construct($browser, $browserUrl) {
    $this->s = new NgnSeleniumStrict($browser, $browserUrl);
  }

  public function __call($method, $param) {
    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], $param);
    } elseif (method_exists($this->s, $method)) {
      return call_user_func_array([$this->s, $method], $param);
    } else {
      throw new NoMethodException($method);
    }
  }

  public function waitForDialog() {
    $this->waitForCondition("selenium.isElementTextNotPresent('#dlg_message', 'Загрузка')");
  }
  
  public function waitForFileDialog() {
    $this->waitForCondition("selenium.isElementTextNotPresent('#dlg_message', 'Загрузка')");
    sleep(2);
  }
  
  public function treeClick($title) {
    $locator = "#ngn-tree-1 span:contains($title)";
    $this->s->mouseMoveAt($locator, '');
    $this->s->mouseDownAt($locator, '');
  }
  
  public function waitForElementPresent($locator, $timeout = self::DEFAULT_TIMEOUT) {
    return $this->waitForCondition("selenium.isElementPresent('$locator')", $timeout);
  }
  
  public function waitForElementNotPresent($locator, $timeout = self::DEFAULT_TIMEOUT) {
    return $this->waitForCondition("!selenium.isElementPresent('$locator')", $timeout);
  }
  
  public function waitAndClick($locator) {
    if ($this->waitForElement($locator))
      $this->s->click("$locator");
    else {
      throw new NgnException("Locator '$locator' not found");
    }
  }
  
  public function waitForDialogClosed($timeout = self::DEFAULT_TIMEOUT) {
    $this->waitForCondition("!selenium.isElementPresent('#dlg_message')", $timeout);
  }
  
  public function okDialog($timeout = self::DEFAULT_TIMEOUT) {
    $this->click('#dlg_ok');
    $this->waitForDialogClosed($timeout);
  }
  
  public function cancelDialog() {
    $this->click('#dlg_cancel');
    $this->waitForDialogClosed();
  }
  
  public function waitForCondition($cmd, $timeout = self::DEFAULT_TIMEOUT) {
    $r = $this->s->waitForCondition(str_replace("\n", ' ', $cmd), $timeout);
    if (strstr($r, 'Timed out') !== false)
      throw new NgnException("Condition '$cmd' timeout");
  }
  
  public function waitForPageToLoad($timeout = self::DEFAULT_TIMEOUT) {
    $this->s->waitForPageToLoad($timeout);
  }
  
  public function loadComplete($locator, $class = 'loader') {
    $this->waitForCondition("!selenium.hasClass('$locator', '$class')");
  }

}