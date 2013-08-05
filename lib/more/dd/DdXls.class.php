<?php

class DdXls {
use LongJob;

  protected $items, $ddo;

  function __construct(DdItems $items) {
    $this->items = $items;
    $this->hasOutput = false;
    Dir::make(UPLOAD_PATH.'/temp/admin/xls');
    $this->ddo = new Ddo($items->strName, 'xls', ['fieldOptions' => ['getAll' => true]]);
    $this->ddo->text = true;
    $this->init();
  }

  protected function init() {
  }

  protected function getItems() {
    return $this->items->getItems();
  }

  function url() {
    $fileName = '/temp/admin/xls/'.$this->items->strName.'_'.date('d-m-Y_H-i-s').'.xls';
    $total = $this->items->count();
    $step = 30;
    $n = 0;
    set_time_limit(0);
    while (1) {
      if (!$this->runner->status()) return; // если задача снята, выходим из цикла
      $this->items->cond->setLimit("$n,$step");
      $before = Misc::formatPrice(memory_get_usage());
      $this->setPercentage(round($n / $total * 100));
      $this->ddo->setItems($this->getItems())->xls(UPLOAD_PATH.$fileName, !(bool)$n);
      LogWriter::str('ddxls', "QUEUE N={{$this->queueN}}. $n, ".($n + $step).' - '.$before.' - '.Misc::formatPrice(memory_get_usage()));
      if ($n >= $total) break;
      $n += $step;
    }
    return '/'.UPLOAD_DIR.$fileName;
  }

}