<?php

class DmfaTypoTextarea extends Dmfa {

  function form2sourceFormat($v) {
    return O::get('FormatText')->cfgSetAutoBrMode(true)->typo($v);
  }
  
  function source2formFormat($v) {
    return str_replace('<br/>', '', $v);
  }

}
