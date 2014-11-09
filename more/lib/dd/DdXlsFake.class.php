<?php

class DdXlsFake extends DdXls {

  protected function step() {
    return 50;
  }

  function iteration() {
    parent::iteration();
    file_put_contents(UPLOAD_PATH.$this->fileName.'.ids', ' ['.implode(', ', $this->items->ids()).']', FILE_APPEND);
  }

}