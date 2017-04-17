<?php

class CliAccessTestCommandsSingle extends ArrayAccessebleOptions {

  static $requiredOptions = ['name'];

  /**
   * @options something
   */
  function a_one() {
    print $this->options['name'].$this->options['something'];
  }

  /**
   * @options something, {else}
   */
  function a_two() {
    print $this->options['name'].$this->options['something'];
    if (isset($this->options['else'])) print $this->options['else'];
  }

  /**
   * @options something, {@another}
   */
  function a_three() {
    print $this->options['name'].$this->options['something'];
    if (isset($this->options['another'])) print $this->options['another'];
  }

  static function helpOpt_another() {
    return ['123'];
  }

}
