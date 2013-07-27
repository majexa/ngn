<?php

class FieldETextTypo extends FieldEText {

  function beforeCU_text(DdItemsManagerPage $dm) {
    /* @var $oFormatText FormatText */
    $oFormatText = O::get('FormatText');
    $dm->data[$this->options['name'].'_f'] = $oFormatText->typo($this->options['value']);
  }

}
