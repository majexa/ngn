<?php

class CliHelpMultiFixture extends ArrayAccessebleOptions {

  static $requiredOptions = ['name1'];

  /**
   * @options name2, name3
   */
  function a_asd() {
    Arr::checkEmpty($this->options, ['name1', 'name2', 'name3']);
  }

}