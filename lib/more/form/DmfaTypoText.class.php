<?php

class DmfaTypoText extends Dmfa {

  function form2sourceFormat($v) {
    return O::get('FormatText')->typo($v);
  }

}
