<?php

class CliColors {

  private $foregroundColors = [];
  private $backgroundColors = [];

  function __construct() {
    $this->foregroundColors['black'] = '0;30';
    $this->foregroundColors['darkGray'] = '1;30';
    $this->foregroundColors['blue'] = '0;34';
    $this->foregroundColors['lightBlue'] = '1;34';
    $this->foregroundColors['green'] = '0;32';
    $this->foregroundColors['lightGreen'] = '1;32';
    $this->foregroundColors['cyan'] = '0;36';
    $this->foregroundColors['lightCyan'] = '1;36';
    $this->foregroundColors['red'] = '0;31';
    $this->foregroundColors['lightRed'] = '1;31';
    $this->foregroundColors['purple'] = '0;35';
    $this->foregroundColors['lightPurple'] = '1;35';
    $this->foregroundColors['brown'] = '0;33';
    $this->foregroundColors['yellow'] = '1;33';
    $this->foregroundColors['lightGray'] = '0;37';
    $this->foregroundColors['white'] = '1;37';
    $this->backgroundColors['black'] = '40';
    $this->backgroundColors['red'] = '41';
    $this->backgroundColors['green'] = '42';
    $this->backgroundColors['yellow'] = '43';
    $this->backgroundColors['blue'] = '44';
    $this->backgroundColors['magenta'] = '45';
    $this->backgroundColors['cyan'] = '46';
    $this->backgroundColors['lightGray'] = '47';
  }

  function getColoredString($string, $foregroundColor = null, $backgroundColor = null) {
    $colored = "";
    if (isset($this->foregroundColors[$foregroundColor])) {
      $colored .= "\033[".$this->foregroundColors[$foregroundColor]."m";
    }
    if (isset($this->backgroundColors[$backgroundColor])) {
      $colored .= "\033[".$this->backgroundColors[$backgroundColor]."m";
    }
    $colored .= $string."\033[0m";
    return $colored;
  }

  function getForegroundColors() {
    return array_keys($this->foregroundColors);
  }

  function getBackgroundColors() {
    return array_keys($this->backgroundColors);
  }
  
}