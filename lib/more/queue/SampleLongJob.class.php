<?php

class SampleLongJob extends LongJobCycle {

  function id() {
    return 'sample';
  }

  protected function _total() {
    return 10;
  }

  protected function step() {
    return 1;
  }

  protected function iteration() {
    usleep(0.1 * 1000000);
  }

  protected function result() {
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
