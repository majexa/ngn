<?php

class CsvWriter implements LineWriterInterface {

  protected $fp;

  function __construct($file) {
    $this->fp = fopen($file, 'a');
  }

  function writeLine(array $fields) {
    fputcsv($this->fp, $fields);
  }

  function __destruct() {
    if (isset($this->fp)) fclose($this->fp);
  }

}