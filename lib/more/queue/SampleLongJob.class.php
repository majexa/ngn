<?php

class SampleLongJob extends LongJobAbstract {

  protected $id;

  function __construct($id = 'sample') {
    $this->id = $id;
    if ($this->step() > $this->_total()) {
      throw new Exception('Step ' . $this->step() . ' can not be bigger then total ' . $this->_total());
    }
    parent::__construct();
  }

  function id() {
    return $this->id;
  }

  protected function _total() {
    return 100;
  }

  protected function step() {
    return 1;
  }

  function iteration() {
    usleep(0.1 * 1000000);
  }

  public function result() {
    return ['all ok'];
  }

}

/*
`sudo install worker $name`;
`sudo /etc/init.d/$name-queue start`;
`run tests`;
`sudo /etc/init.d/$name-queue stop`;
`sudo uninstall worker $name`;
*/
// инсталлировать воркер
// запустить воркер
// стартонуть одну и ту же работу 2 раза. проверить, что 2-й раз не стартонулось
// стартонуть, остановить, стартонуть. проверить, что остановилось
// остановить воркер
// деинсталлировать воркер
