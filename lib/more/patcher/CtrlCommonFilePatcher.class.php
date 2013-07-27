<?php

class CtrlCommonFilePatcher extends CtrlCommonPatcher {
  
  protected function getPatcher() {
    return O::get('FilePatcher');
  }
  
}