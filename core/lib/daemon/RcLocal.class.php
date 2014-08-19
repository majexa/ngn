<?php

class RcLocal {

  function __construct() {
    Misc::checkConst('TEMP_PATH');
    $this->c = file_get_contents('/etc/rc.local');
  }

  protected function hasNgnWorkers() {
    return strstr($this->c, '# ngn auto-generated workers');
  }

  protected function rcLocalWrite($c) {
    $tmpFile = TEMP_PATH.'/'.Misc::randString();
    file_put_contents($tmpFile, $c);
    $this->c = $c;
    `sudo mv $tmpFile /etc/rc.local`;
  }

  protected $begin = "# ngn auto-generated workers begin\nsleep 15";
  protected $end = "# ngn auto-generated workers end";

  function add($initDName) {
    $cmd = "su user -c 'sudo /etc/init.d/$initDName start'";
    if (!$this->hasNgnWorkers()) {
      $this->rcLocalWrite("\n\n$this->begin\n$cmd\n$this->end\n\n".$this->c);
    }
    else {
      $m = $this->checkExistence();
      if (strstr($m[1], $cmd)) {
        output("Worker '$initDName' already in rc.local");
      }
      else {
        $this->rcLocalWrite(preg_replace("/($this->begin)(.*)($this->end)/ms", '$1$2'."$cmd\n".'$3', $this->c));
      }
    }
  }

  protected function checkExistence() {
    if (!preg_match("/$this->begin(.*)$this->end/ms", $this->c, $m)) throw new Exception('ngn worker records not found');
    return $m;
  }

  function remove($initDName) {
    throw new Exception('not realized');
  }

  function cleanup() {
    $this->checkExistence();
    $this->rcLocalWrite(preg_replace("/($this->begin)(.*)($this->end)/ms", '$1$2$3', $this->c));
  }

}