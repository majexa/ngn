<?php

class CliColors {

  static $foregroundColors = [];
  static $backgroundColors = [];

  static function colored($string, $foregroundColor = null, $backgroundColor = null) {
    if (getOS() == 'win') return $string;
    $colored = "";
    if (isset(CliColors::$foregroundColors[$foregroundColor])) {
      $colored .= "\033[".CliColors::$foregroundColors[$foregroundColor]."m";
    }
    if (isset(CliColors::$backgroundColors[$backgroundColor])) {
      $colored .= "\033[".CliColors::$backgroundColors[$backgroundColor]."m";
    }
    $colored .= $string."\033[0m";
    return $colored;
  }

}

CliColors::$foregroundColors['black'] = '0;30';
CliColors::$foregroundColors['darkGray'] = '1;30';
CliColors::$foregroundColors['blue'] = '0;34';
CliColors::$foregroundColors['lightBlue'] = '1;34';
CliColors::$foregroundColors['green'] = '0;32';
CliColors::$foregroundColors['lightGreen'] = '1;32';
CliColors::$foregroundColors['cyan'] = '0;36';
CliColors::$foregroundColors['lightCyan'] = '1;36';
CliColors::$foregroundColors['red'] = '0;31';
CliColors::$foregroundColors['lightRed'] = '1;31';
CliColors::$foregroundColors['purple'] = '0;35';
CliColors::$foregroundColors['lightPurple'] = '1;35';
CliColors::$foregroundColors['brown'] = '0;33';
CliColors::$foregroundColors['yellow'] = '1;33';
CliColors::$foregroundColors['lightGray'] = '0;37';
CliColors::$foregroundColors['white'] = '1;37';
CliColors::$backgroundColors['black'] = '40';
CliColors::$backgroundColors['red'] = '41';
CliColors::$backgroundColors['green'] = '42';
CliColors::$backgroundColors['yellow'] = '43';
CliColors::$backgroundColors['blue'] = '44';
CliColors::$backgroundColors['magenta'] = '45';
CliColors::$backgroundColors['cyan'] = '46';
CliColors::$backgroundColors['lightGray'] = '47';
