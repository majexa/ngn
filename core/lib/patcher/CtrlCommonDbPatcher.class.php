<?php

class CtrlCommonDbPatcher extends CtrlCommonPatcher {
  
  protected function getPatcher() {
    return O::get('DbPatcher');
  }

}