<?php

class TestCurl extends Curl {

  protected function init() {
    parent::init();
    $this->setopt(CURLOPT_COOKIE, 'debugKey='.Misc::checkEmpty(Ngn::debugKey()));
  }

}