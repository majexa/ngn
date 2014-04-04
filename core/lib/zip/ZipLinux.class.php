<?php

class ZipLinux extends ZipExtended {

  function dir($archive, $path, $localpath = null) {
    sys("zip -r $archive $path");
  }
  
  function file($archive, $file, $localpath = null) {
    sys("zip $archive $file");
  }
  
  function lst($archive) {
  }
  
  protected function _extract($from, $to) {
    sys("unzip $from -d$to");
  }

}
