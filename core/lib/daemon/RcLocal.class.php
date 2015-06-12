<?php

class RcLocal {

  function __construct() {
    Misc::checkConst('TEMP_PATH');
    $this->c = file_get_contents('/etc/rc.local');
  }

  protected function hasNgnWorkers() {
    return strstr($this->c, '# ngn auto-generated workers');
  }

  protected function write($c) {
    $tmpFile = Dir::make(TEMP_PATH).'/'.Misc::randString();
    file_put_contents($tmpFile, $c);
    $this->c = $c;
    `sudo mv $tmpFile /etc/rc.local`;
  }

  protected $begin = "# ngn auto-generated workers begin\nsleep 15";
  protected $end = "# ngn auto-generated workers end";

  protected function cmd($initDName) {
    return "su user -c 'sudo /etc/init.d/$initDName start'";
  }

  /**
   * Добавляет автозагрузчик
   *
   * @param string $initDName Имя файла в папке /etc/init.d/
   * @throws Exception
   */
  function add($initDName) {
    $cmd = $this->cmd($initDName);
    if (!$this->hasNgnWorkers()) {
      $this->write("\n\n$this->begin\n$cmd\n$this->end\n\n".$this->c);
    }
    else {
      $m = $this->checkExistence();
      if (strstr($m[1], $cmd)) {
        output("Worker '$initDName' already in rc.local");
      }
      else {
        $this->write(preg_replace("/($this->begin)(.*)($this->end)/ms", '$1$2'."$cmd\n".'$3', $this->c));
      }
    }
  }

  /**
   * Удаляет автозагрузчик
   *
   * @param string $initDName Имя файла в папке /etc/init.d/
   * @throws Exception
   */
  function remove($initDName) {
    $this->write(str_replace($this->cmd($initDName)."\n", '', file_get_contents('/etc/rc.local')));
  }

  protected function checkExistence() {
    if (!preg_match("/$this->begin(.*)$this->end/ms", $this->c, $m)) throw new Exception('ngn worker records not found');
    return $m;
  }

  function cleanup() {
    $this->checkExistence();
    $this->write(preg_replace("/($this->begin)(.*)($this->end)/ms", '$1$2$3', $this->c));
  }

}