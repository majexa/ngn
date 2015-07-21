<?php

class DmfaDateSchedule extends Dmfa {

  function elAfterCreateUpdate(FieldEDateSchedule $el) {
    die2('-');
    prr($el['value']);
  }

}