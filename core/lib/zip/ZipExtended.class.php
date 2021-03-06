<?php

abstract class ZipExtended {

  abstract function dir($archive, $path, $localpath = null);
  abstract function file($archive, $file, $localpath = null);
  abstract function lst($archive);
  abstract protected function _extract($from, $to);

  const tempDirName = 'zip_temp';
  
  /**
   * ................
   *
   * @param   string  Архив
   * @param   string  Каталог
   * @param   bool    Выкидывать исключение
   * @return  string  Путь до распакованного файла/каталога
   */
  function extract($from, $to, $strict = true) {
    if (!file_exists($from))
      throw new Exception("File '$from' does not exists");
    $toTemp = $to.'/'.self::tempDirName;
    Dir::remove($toTemp);
    output("Try to create folder '$toTemp'");
    if (!is_writable(dirname($toTemp)))
      throw new Exception("Folder '".dirname($toTemp)."' is not writeble");
    mkdir($toTemp);
    $r = $this->_extract($from, $toTemp, $strict);
    Dir::chmod($toTemp, 0777);
    foreach (Dir::get($toTemp) as $v) {
      $ddd = dirname($toTemp).'/'.$v;
      if (file_exists($ddd)) Dir::remove($ddd);
      rename($toTemp.'/'.$v, $ddd);
    }
    Dir::remove($toTemp);
    return $r;    
  }
  
}