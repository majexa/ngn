<?php

class DdXlsFake extends DdXls {

  function iteration() {
    parent::iteration();
    file_put_contents(UPLOAD_PATH.$this->fileName.'.ids', ' ['.implode(', ', $this->items->getItemIds()).']', FILE_APPEND);
  }

}