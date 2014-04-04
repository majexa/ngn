<?php

class FieldETextTypo extends FieldEText {

  function beforeCU_text(DdItemsManagerPage $dm) {
    /* @var $formatText FormatText */
    $formatText = O::get('FormatText');
    $dm->data[$this->options['name'].'_f'] = $formatText->typo($this->options['value']);
  }

}
