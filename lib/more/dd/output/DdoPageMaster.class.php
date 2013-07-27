<?php

class DdoPageMaster extends DdoPage {
  
  protected function initTpls() {
    parent::initTpls();
    $this->ddddByName['title'] = 
      'getPrr($o->pageSettings)';
  }
  
}
