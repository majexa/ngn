<?php

class DdXlsFake extends DdXls {

  function iteration() {
    parent::iteration();
    LogWriter::str('ddXlsFake', $this->n.','.$this->step());
    //LogWriter::str('ddXlsFake', $this->items->cond->all());
    file_put_contents(UPLOAD_PATH.$this->fileName.'.ids', ' ['.implode(', ', $this->items->getItemIds()).']', FILE_APPEND);
  }

}