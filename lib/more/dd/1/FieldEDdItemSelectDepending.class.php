<?php

class FieldEDdItemSelectDepending extends FieldEDdItemSelect {

  function _html() {
    return parent::_html().getPrr($this->options);
  }

}